export function audioReport() {
    return {
        mode: 'idle',           // idle | recording | recorded
        mediaRecorder: null,
        recognition: null,
        chunks: [],
        audioBlob: null,
        audioUrl: null,
        transcription: '',
        interim: '',
        seconds: 0,
        timerInterval: null,
        micError: '',
        speechSupported: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
        insecureContext: !window.isSecureContext,
        recognitionActive: false,
        stoppingIntentionally: false,
        baseTranscription: '',
        transcribing: false,
        transcribeError: '',

        get timerDisplay() {
            const m = String(Math.floor(this.seconds / 60)).padStart(2, '0');
            const s = String(this.seconds % 60).padStart(2, '0');
            return m + ':' + s;
        },

        async startRecording() {
            this.micError      = '';
            this.transcription = '';
            this.interim       = '';
            this.baseTranscription = '';
            this.stoppingIntentionally = false;

            if (this.insecureContext) {
                this.micError = 'El sitio no está en una conexión segura (https), así que el navegador bloquea el micrófono. Avisa al administrador.';
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.chunks    = [];
                this.audioBlob = null;
                this.audioUrl  = null;
                this.seconds   = 0;

                // Guardar audio como blob
                this.mediaRecorder = new MediaRecorder(stream);
                this.mediaRecorder.ondataavailable = e => {
                    if (e.data.size > 0) this.chunks.push(e.data);
                };
                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.chunks, { type: 'audio/webm' });
                    this.audioUrl  = URL.createObjectURL(this.audioBlob);
                    stream.getTracks().forEach(t => t.stop());
                    this.mode = 'recorded';
                    this.transcribeOnServer();
                };
                this.mediaRecorder.start(250);

                // Transcripción en tiempo real con Web Speech API
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (SR) {
                    this.recognition = new SR();
                    this.recognition.lang = 'es-ES';
                    this.recognition.continuous = true;
                    this.recognition.interimResults = true;
                    this.recognition.onresult = (e) => {
                        let final = '';
                        let interimText = '';
                        for (let i = 0; i < e.results.length; i++) {
                            if (e.results[i].isFinal) {
                                final += e.results[i][0].transcript + ' ';
                            } else {
                                interimText += e.results[i][0].transcript;
                            }
                        }
                        this.transcription = (this.baseTranscription + ' ' + final).trim();
                        this.interim = interimText;
                    };
                    this.recognition.onerror = (e) => {
                        this.recognitionActive = false;
                        const mensajes = {
                            'not-allowed': 'El navegador bloqueó el acceso al micrófono para transcribir. Revisa los permisos.',
                            'service-not-allowed': 'El navegador bloqueó el servicio de transcripción. Revisa los permisos.',
                            'network': 'Sin conexión a internet para transcribir. El audio se sigue grabando, pero escribe la descripción a mano.',
                            'audio-capture': 'No se detectó micrófono para transcribir.',
                        };
                        if (mensajes[e.error]) this.micError = mensajes[e.error];
                        // 'no-speech' y 'aborted' son normales (pausas), no se muestran como error.
                    };
                    this.recognition.onend = () => {
                        this.recognitionActive = false;
                        // Chrome en Android corta el reconocimiento aunque sea "continuous"; lo reiniciamos si seguimos grabando.
                        if (this.mode === 'recording' && !this.stoppingIntentionally) {
                            try {
                                this.baseTranscription = this.transcription;
                                this.recognition.start();
                                this.recognitionActive = true;
                            } catch (err) { /* ya estaba iniciado o se detuvo justo ahora */ }
                        }
                    };
                    this.recognition.start();
                    this.recognitionActive = true;
                }

                this.mode = 'recording';
                this.timerInterval = setInterval(() => this.seconds++, 1000);
            } catch (e) {
                this.micError = 'No se pudo acceder al micrófono. Revisa los permisos del navegador.';
            }
        },

        async transcribeOnServer() {
            if (!this.audioBlob) return;

            this.transcribing = true;
            this.transcribeError = '';

            try {
                const form = new FormData();
                form.append('audio', this.audioBlob, 'recording.webm');

                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/reports/transcribe', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: form,
                });

                const data = await res.json();

                if (!res.ok || !data.text) {
                    throw new Error(data.error || 'No se pudo transcribir el audio.');
                }

                // La transcripción del servidor (Whisper) es más confiable que la del navegador;
                // reemplaza lo que haya capturado el reconocimiento en vivo, si lo hubo.
                this.transcription = data.text.trim();
                this.baseTranscription = this.transcription;
                this.interim = '';
            } catch (e) {
                // Si ya había texto del reconocimiento en vivo del navegador, lo dejamos tal cual.
                if (!this.transcription) {
                    this.transcribeError = 'No se pudo transcribir automáticamente. Escribe la descripción a mano.';
                }
            } finally {
                this.transcribing = false;
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mode === 'recording') {
                clearInterval(this.timerInterval);
                this.stoppingIntentionally = true;
                if (this.recognition) {
                    this.recognition.stop();
                    // Espera breve para que lleguen los últimos resultados
                    setTimeout(() => { this.interim = ''; }, 300);
                }
                this.mediaRecorder.stop();
            }
        },

        reset() {
            clearInterval(this.timerInterval);
            this.stoppingIntentionally = true;
            if (this.mediaRecorder && this.mode === 'recording') this.mediaRecorder.stop();
            if (this.recognition) this.recognition.stop();
            this.mode          = 'idle';
            this.chunks        = [];
            this.audioBlob     = null;
            this.audioUrl      = null;
            this.transcription = '';
            this.interim       = '';
            this.micError      = '';
            this.transcribeError = '';
            this.transcribing   = false;
            this.seconds       = 0;
        },
    };
}
