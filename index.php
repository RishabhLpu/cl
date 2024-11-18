<?php
session_start();
include('includes/config.php');
if (isset($_POST['login'])) {
    $emailreg = $_POST['emailreg'];
    $password = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT email, password, id FROM userregistration WHERE (email=? || regNo=?) and password=? ");
    $stmt->bind_param('sss', $emailreg, $emailreg, $password);
    $stmt->execute();
    $stmt->bind_result($email, $password, $id);
    $rs = $stmt->fetch();
    $stmt->close();
    $_SESSION['id'] = $id;
    $_SESSION['login'] = $emailreg;
    $uip = $_SERVER['REMOTE_ADDR'];
    $ldate = date('d/m/Y h:i:s', time());
    if ($rs) {
        $uid = $_SESSION['id'];
        $uemail = $_SESSION['login'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $geopluginURL = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
        $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
        $city = $addrDetailsArr['geoplugin_city'];
        $country = $addrDetailsArr['geoplugin_countryName'];
        $log = "insert into userLog(userId, userEmail, userIp, city, country) values('$uid', '$uemail', '$ip', '$city', '$country')";
        $mysqli->query($log);
        if ($log) {
            header("location:dashboard.php");
        }
    } else {
        echo "<script>alert('Invalid Username/Email or password');</script>";
    }
}
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Student Hostel Registration</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <style>
        .chat-trigger {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            padding: 15px;
            cursor: pointer;
            font-size: 18px;
        }

        .chatbot-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .chat-header {
            background: #007bff;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .chat-body {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            font-size: 14px;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .chat-footer input {
            width: calc(100% - 60px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chat-footer button {
            padding: 8px 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .bot-message {
            background: #f1f1f1;
            color: #000;
        }

        .user-message {
            background: #007bff;
            color: #fff;
            text-align: right;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatTrigger = document.getElementById('chatTrigger');
            const chatbotContainer = document.getElementById('chatbotContainer');
            
            // Toggle the chatbot container visibility
            chatTrigger.addEventListener('click', function() {
                // If the chatbot is hidden, show it
                if (chatbotContainer.style.display === 'none' || chatbotContainer.style.display === '') {
                    chatbotContainer.style.display = 'block';
                } else {
                    // If the chatbot is visible, hide it
                    chatbotContainer.style.display = 'none';
                }
            });
        });

        function handleBotResponse(question) {
            appendUserMessage(question);
            let response = '';
            switch (question) {
                case 'What courses do you offer?':
                    response = 'We offer courses in Full Stack Development, Data Science, Machine Learning, and more.';
                    break;
                case 'What is the fee structure?':
                    response = 'The fee structure varies by course. Please visit our website for detailed pricing.';
                    break;
                case 'Do you offer online classes?':
                    response = 'Yes, we offer both online and offline classes.';
                    break;
                case 'How do I enroll?':
                    response = 'You can enroll by visiting our website or contacting our support team.';
                    break;
                case 'Can I get a demo?':
                    response = 'Yes, we provide a free demo session. Please contact support to schedule.';
                    break;
                default:
                    response = 'Sorry, I did not understand that.';
            }
            appendBotMessage(response);
        }

        function appendUserMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', 'user-message');
            messageDiv.textContent = message;
            document.getElementById('chatBody').appendChild(messageDiv);
            document.getElementById('chatBody').scrollTop = document.getElementById('chatBody').scrollHeight;
        }

        function appendBotMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', 'bot-message');
            messageDiv.textContent = message;
            document.getElementById('chatBody').appendChild(messageDiv);
            document.getElementById('chatBody').scrollTop = document.getElementById('chatBody').scrollHeight;
        }

        function sendMessage() {
            const userInput = document.getElementById('userInput').value.trim();
            if (userInput) {
                appendUserMessage(userInput);
                document.getElementById('userInput').value = '';
                setTimeout(() => {
                    appendBotMessage("Thank you for your question! Please wait while I find an answer.");
                }, 500);
            }
        }
    </script>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/sidebar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="page-title">User Login</h2>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="well row pt-2x pb-3x bk-light">
                            <div class="col-md-8 col-md-offset-2">
                                <form action="" method="post">
                                    <label for="" class="text-uppercase text-sm">Email / Registration Number</label>
                                    <input type="text" name="emailreg" class="form-control mb" required>
                                    <label for="" class="text-uppercase text-sm">Password</label>
                                    <input type="password" name="password" class="form-control mb" required>
                                    <input type="submit" name="login" class="btn btn-primary btn-block" value="login">
                                </form>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="forgot-password.php">Forgot password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Trigger -->
    <button class="chat-trigger" id="chatTrigger">💬</button>

    <!-- Chatbot Container -->
    <div class="chatbot-container" id="chatbotContainer">
        <div class="chat-header">Chat with Us!</div>
        <div class="chat-body" id="chatBody">
            <div class="message bot-message">Hello! How can I assist you today?</div>
            <div class="message bot-message">
                <button onclick="handleBotResponse('What courses do you offer?')">What courses do you offer?</button>
                <button onclick="handleBotResponse('What is the fee structure?')">What is the fee structure?</button>
                <button onclick="handleBotResponse('Do you offer online classes?')">Do you offer online classes?</button>
                <button onclick="handleBotResponse('How do I enroll?')">How do I enroll?</button>
                <button onclick="handleBotResponse('Can I get a demo?')">Can I get a demo?</button>
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
</body>

</html>
