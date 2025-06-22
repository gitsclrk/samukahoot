<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dagat quiz</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Comic+Neue:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    
    <div class="container">
        <!-- Welcome Screen -->
        <div id="welcome-screen">
            <h1 class="title">🎯 ultimate miyans quiz</h1>
            <p class="subtitle">gaano ba natin kilala ang isa't isa?</p>
            
            <div class="name-input-container">
                <label for="player-name">Enter your name:</label>
                <input type="text" id="player-name" placeholder="Type your name here..." maxlength="20">
            </div>
            
            <button class="start-btn" onclick="startQuiz()" id="start-btn" disabled>Start Quiz</button>
        </div>

        <!-- Quiz Screen -->
        <div id="quiz-container" class="quiz-container">
            <div class="timer" id="timer">Time: 30s</div>
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
            <div class="score-display" id="score-display">Score: 0</div>
            
            <button class="exit-btn" onclick="exitQuiz()">Exit Quiz</button>
            
            <div class="question" id="question"></div>
            <div class="options" id="options"></div>
            
            <button class="start-btn" onclick="nextQuestion()" id="next-btn" style="display: none;">Next Question</button>
        </div>

        <!-- Results Screen -->
        <div id="result-container" class="result-container">
            <h2 class="final-score">Quiz Complete!</h2>
            <div class="score-circle" id="score-circle">0%</div>
            <div id="final-message"></div>
            <button class="start-btn" onclick="restartQuiz()">Play Again</button>
        </div>
    </div>

    <script>
        // Quiz data
        const quizData = [
            {
                question: "What is the capital of France?",
                options: ["London", "Berlin", "Paris", "Madrid"],
                correct: 2
            },
            {
                question: "Which planet is known as the Red Planet?",
                options: ["Venus", "Mars", "Jupiter", "Saturn"],
                correct: 1
            },
            {
                question: "What is the largest mammal in the world?",
                options: ["African Elephant", "Blue Whale", "Giraffe", "Hippopotamus"],
                correct: 1
            },
            {
                question: "Who painted the Mona Lisa?",
                options: ["Vincent van Gogh", "Pablo Picasso", "Leonardo da Vinci", "Michelangelo"],
                correct: 2
            },
            {
                question: "What is the chemical symbol for gold?",
                options: ["Ag", "Au", "Fe", "Cu"],
                correct: 1
            },
            {
                question: "What is the hardest natural substance on Earth?",
                options: ["Steel", "Diamond", "Granite", "Iron"],
                correct: 1
            },
            {
                question: "What is the smallest unit of life?",
                options: ["Atom", "Cell", "Molecule", "Tissue"],
                correct: 1
            },
            {
                question: "What gas do plants absorb from the atmosphere?",
                options: ["Oxygen", "Nitrogen", "Carbon Dioxide", "Hydrogen"],
                correct: 2
            },
            {
                question: "What is the speed of light?",
                options: ["299,792 km/s", "199,792 km/s", "399,792 km/s", "499,792 km/s"],
                correct: 0
            },
            {
                question: "Rin, will you marry me?",
            }
        ];

        let currentQuestion = 0;
        let score = 0;
        let timer;
        let timeLeft = 30;
        let questions = [];
        let playerName = '';

        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Handle name input
        document.getElementById('player-name').addEventListener('input', function() {
            const name = this.value.trim();
            const startBtn = document.getElementById('start-btn');
            
            if (name.length > 0) {
                startBtn.disabled = false;
                playerName = name;
            } else {
                startBtn.disabled = true;
                playerName = '';
            }
        });

        // Allow Enter key to start quiz
        document.getElementById('player-name').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !this.value.trim() === '') {
                startQuiz();
            }
        });

        function startQuiz() {
            if (!playerName.trim()) {
                alert('Please enter your name first!');
                return;
            }
            
            questions = quizData;
            currentQuestion = 0;
            score = 0;
            timeLeft = 30;
            
            document.getElementById('welcome-screen').style.display = 'none';
            document.getElementById('quiz-container').style.display = 'block';
            
            showQuestion();
            startTimer();
        }

        function showQuestion() {
            const question = questions[currentQuestion];
            document.getElementById('question').textContent = question.question;
            
            const optionsContainer = document.getElementById('options');
            optionsContainer.innerHTML = '';
            
            question.options.forEach((option, index) => {
                const optionElement = document.createElement('div');
                optionElement.className = 'option';
                optionElement.textContent = option;
                optionElement.onclick = () => selectOption(index);
                optionsContainer.appendChild(optionElement);
            });
            
            updateProgress();
        }

        function selectOption(selectedIndex) {
            const options = document.querySelectorAll('.option');
            const correctAnswer = questions[currentQuestion].correct;
            
            // Disable all options
            options.forEach(option => {
                option.style.pointerEvents = 'none';
            });
            
            // Show correct/incorrect
            if (selectedIndex === correctAnswer) {
                options[selectedIndex].classList.add('correct');
                score += 10;
                score += Math.max(0, timeLeft * 2); // Bonus points for time remaining
            } else {
                options[selectedIndex].classList.add('incorrect');
                options[correctAnswer].classList.add('correct');
            }
            
            document.getElementById('score-display').textContent = `Score: ${score}`;
            document.getElementById('next-btn').style.display = 'inline-block';
            
            clearInterval(timer);
        }

        function nextQuestion() {
            currentQuestion++;
            timeLeft = 30;
            
            if (currentQuestion < questions.length) {
                showQuestion();
                startTimer();
                document.getElementById('next-btn').style.display = 'none';
            } else {
                showResults();
            }
        }

        function startTimer() {
            clearInterval(timer);
            timeLeft = 30;
            
            timer = setInterval(() => {
                timeLeft--;
                document.getElementById('timer').textContent = `Time: ${timeLeft}s`;
                
                if (timeLeft <= 10) {
                    document.getElementById('timer').classList.add('warning');
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    selectOption(-1); // Time's up
                }
            }, 1000);
        }

        function updateProgress() {
            const progress = ((currentQuestion + 1) / questions.length) * 100;
            document.getElementById('progress-fill').style.width = progress + '%';
        }

        function showResults() {
            document.getElementById('quiz-container').style.display = 'none';
            document.getElementById('result-container').style.display = 'block';
            
            const percentage = Math.round((score / (questions.length * 10)) * 100);
            const scoreCircle = document.getElementById('score-circle');
            const finalMessage = document.getElementById('final-message');
            
            scoreCircle.textContent = percentage + '%';
            scoreCircle.style.background = `conic-gradient(from 0deg, #4CAF50 0deg, #4CAF50 ${percentage * 3.6}deg, #e0e0e0 ${percentage * 3.6}deg)`;
            
            if (percentage >= 80) {
                finalMessage.innerHTML = `<h3>🎉 Excellent, ${playerName}! You're a quiz master!</h3>`;
            } else if (percentage >= 60) {
                finalMessage.innerHTML = `<h3>👍 Good job, ${playerName}! Well done!</h3>`;
            } else if (percentage >= 40) {
                finalMessage.innerHTML = `<h3>😊 Not bad, ${playerName}! Keep practicing!</h3>`;
            } else {
                finalMessage.innerHTML = `<h3>📚 Keep learning, ${playerName}! You'll get better!</h3>`;
            }
            
            finalMessage.innerHTML += `<p>Final Score: ${score} points</p>`;
        }

        function restartQuiz() {
            document.getElementById('result-container').style.display = 'none';
            document.getElementById('welcome-screen').style.display = 'block';
            document.getElementById('timer').classList.remove('warning');
            
            // Reset name input
            document.getElementById('player-name').value = '';
            document.getElementById('start-btn').disabled = true;
            playerName = '';
        }

        function exitQuiz() {
            // Clear the timer
            clearInterval(timer);
            
            // Reset quiz state
            currentQuestion = 0;
            score = 0;
            timeLeft = 30;
            
            // Reset UI elements
            document.getElementById('timer').textContent = 'Time: 30s';
            document.getElementById('timer').classList.remove('warning');
            document.getElementById('score-display').textContent = 'Score: 0';
            document.getElementById('progress-fill').style.width = '0%';
            document.getElementById('next-btn').style.display = 'none';
            
            // Hide quiz container and show welcome screen
            document.getElementById('quiz-container').style.display = 'none';
            document.getElementById('welcome-screen').style.display = 'block';
        }

        // Initialize particles when page loads
        window.addEventListener('load', createParticles);
    </script>
</body>
</html> 