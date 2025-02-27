<?php
// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['inscription'])) {
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email1 = htmlspecialchars($_POST['email1']);
    $email2 = htmlspecialchars($_POST['email2']);
    $pwd1 = htmlspecialchars($_POST['pwd1']);
    $pwd2 = htmlspecialchars($_POST['pwd2']);

    if ($email1 !== $email2) {
        echo "Les courriels ne correspondent pas.";
    } elseif ($pwd1 !== $pwd2) {
        echo "Les mots de passe ne correspondent pas.";
    } else {
        $servername = "10.56.8.62";
        $username = "navi0007";
        $password = "VMgFL5Bfhb";
        $dbname = "portfolio";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $hashed_password = password_hash($pwd1, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email1);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();

            // Redirection vers admin_messages.php après inscription réussie
            header("Location: admin_messages.php");
            exit;
        } catch(PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="cv.css" />
    <style>
        @import url("https://fonts.cdnfonts.com/css/santor");
        body {
            font-family: Santor, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #page {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"],
        a.button {
            background-color: #f4e5f8;
            color: black;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        input[type="submit"] {
            font-family: Santor, sans-serif;
        }
        input[type="submit"]:hover,
        a.button:hover {
            background-color: #e9ccf1;
        }
        .button-container {
            display: flex;
            justify-content: center;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: black;
            text-decoration: none;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div id="page">
        <div class="button-container">
            <a href="../" class="button">Accueil</a>
        </div>
        <h1>Inscription</h1>
        <form method="post">
            <label>Nom :</label>
            <input type="text" name="lastname" required>
            <label>Prénom :</label>
            <input type="text" name="firstname" required>
            <label>Courriel :</label>
            <input type="email" name="email1" required>
            <label>Confirmation courriel :</label>
            <input type="email" name="email2" required>
            <label>Mot de passe :</label>
            <input type="password" name="pwd1" required>
            <label>Confirmation mot de passe :</label>
            <input type="password" name="pwd2" required>
            <input type="submit" name="inscription" value="Inscription">
        </form>
    </div>
</body>
</html>