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

        get timerDisplay() {
            const m = String(Math.floor(this.seconds / 60)).padStart(2, '0');
            const s = String(this.seconds % 60).padStart(2, '0');
            return m + ':' + s;
        },

        async startRecording() {
            this.micError      = '';
            this.transcription = '';
            this.interim       = '';

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
                        this.transcription = final.trim();
                        this.interim = interimText;
                    };
                    this.recognition.onerror = () => {};
                    this.recognition.start();
                }

                this.mode = 'recording';
                this.timerInterval = setInterval(() => this.seconds++, 1000);
            } catch (e) {
                this.micError = 'No se pudo acceder al micrófono. Revisa los permisos del navegador.';
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mode === 'recording') {
                clearInterval(this.timerInterval);
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
            if (this.mediaRecorder && this.mode === 'recording') this.mediaRecorder.stop();
            if (this.recognition) this.recognition.stop();
            this.mode          = 'idle';
            this.chunks        = [];
            this.audioBlob     = null;
            this.audioUrl      = null;
            this.transcription = '';
            this.interim       = '';
            this.micError      = '';
            this.seconds       = 0;
        },
    };
}
