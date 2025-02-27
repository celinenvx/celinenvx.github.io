<?php
session_start();

// Authentification basique
if (!isset($_SESSION['loggedin'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Connexion à la base de données
        $servername = "10.56.8.62";
        $db_username = "navi0007";
        $db_password = "VMgFL5Bfhb";
        $dbname = "portfolio";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérification des informations d'identification
            $stmt = $conn->prepare("SELECT password FROM users WHERE email = :email");
            $stmt->bindParam(':email', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                header("Location: admin_messages.php");
                exit;
            } else {
                echo 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        } catch(PDOException $e) {
            die("Connexion échouée: " . $e->getMessage());
        }
    } else {
        echo '<div id="auth-page">
                <form method="post" class="auth-form">
                    <h1>Connexion</h1>
                    <label for="username">Nom d\'utilisateur:</label>
                    <input type="text" name="username" class="input-field" required><br>
                    <label for="password">Mot de passe:</label>
                    <input type="password" name="password" class="input-field" required><br>
                    <input type="submit" value="Se connecter" class="button">
                </form>
              </div>';
        exit;
    }
}

// Connexion à la base de données
$servername = "10.56.8.62";
$username = "navi0007";
$password = "VMgFL5Bfhb";
$dbname = "portfolio";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connexion échouée: " . $e->getMessage());
}

// Marquer un message comme traité
if (isset($_POST['mark_as_read'])) {
    $message_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("UPDATE messages SET status='traite' WHERE id=:id");
    $stmt->bindParam(':id', $message_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Supprimer un message traité
if (isset($_POST['delete_message'])) {
    $message_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id=:id AND status='traite'");
    $stmt->bindParam(':id', $message_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Afficher les messages avec pagination et recherche
$show_all = isset($_POST['show_all']) ? true : false;
$search = isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM messages WHERE 1=1";
if (!$show_all) {
    $sql .= " AND status='non-traite'";
}
if ($search) {
    $sql .= " AND (nom LIKE :search OR email LIKE :search)";
}
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
if ($search) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de messages pour la pagination
$count_sql = "SELECT COUNT(*) FROM messages WHERE 1=1";
if (!$show_all) {
    $count_sql .= " AND status='non-traite'";
}
if ($search) {
    $count_sql .= " AND (nom LIKE :search OR email LIKE :search)";
}
$count_stmt = $conn->prepare($count_sql);
if ($search) {
    $count_stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}
$count_stmt->execute();
$total_messages = $count_stmt->fetchColumn();
$total_pages = ceil($total_messages / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Messages</title>
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
        #page, #auth-page {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
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
            width: 100%;
        }
        input[type="submit"],
        button {
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
            font-family: Santor, sans-serif;
        }
        input[type="submit"]:hover,
        button:hover {
            background-color: #e9ccf1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btn {
            background-color: #f4e5f8;
            color: black;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: Santor, sans-serif;
        }
        .action-btn:hover {
            background-color: #e9ccf1;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #333;
        }
        .pagination a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div id="auth-page">
        <form method="post" class="auth-form">
            <h1>Connexion</h1>
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" class="input-field" required><br>
            <label for="password">Mot de passe:</label>
            <input type="password" name="password" class="input-field" required><br>
            <input type="submit" value="Se connecter" class="button">
        </form>
    </div>
    <div id="page">
        <h1>Messages Admin</h1>
        <form method="post">
            <button type="submit" name="show_all"><?php echo $show_all ? "Voir les non-traités" : "Voir tout"; ?></button>
        </form>
        <form method="post">
            <input type="text" name="search" placeholder="Rechercher par nom ou email" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Rechercher</button>
        </form>
        <?php if (count($result) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach($result as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['message']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] == 'non-traite'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <button type="submit" name="mark_as_read" class="action-btn">Marquer comme traité</button>
                    </form>
                    <?php elseif ($row['status'] == 'traite'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <button type="submit" name="delete_message" class="action-btn">Supprimer</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php else: ?>
        <p>Aucun message trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn = null;
?>