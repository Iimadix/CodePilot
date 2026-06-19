<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../config/connect.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$name = isset($data["name"]) ? trim($data["name"]) : "";
$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? trim($data["password"]) : "";

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Заполните все поля"]);
    exit;
}

if (preg_match('/\s/', $name)) {
    echo json_encode(["success" => false, "message" => "Логин не может содержать пробелы"]);
    exit;
}

$forbidden_words = ['хуй', 'хуё', 'хуе', 'хуя', 'пизда', 'пизд', 'пизде', 'ебал', 'ебать', 'ебли', 'ебну', 
    'ёб', 'блядь', 'блять', 'бляд', 'блеать', 'гандон', 'мудак', 'муди', 'пидор', 'пидорас', 
    'педик', 'гомик', 'сука', 'суки', 'тварь', 'урод', 'чмо', 'шалава', 'шлюха', 'шлюх', 
    'долбоёб', 'дебил', 'даун', 'идиот', 'дурак', 'тупица', 'кретин', 'дегенерат', 'ублюдок', 
    'выблядок', 'сучка', 'мразь', 'скотина', 'тварюка', 'падла', 'падлюка', 'гнида', 'сволоч', 
    'гад', 'гадина', 'дрист', 'дерьмо', 'говно', 'какашк', 'залуп', 'манда', 'мозгоёб', 
    'охуел', 'охуеть', 'охереть', 'похуй', 'нахер', 'нихер', 'херня', 'херов', 'хренов', 
    'хую', 'хуц', 'педераст', 'анальн', 'вагин', 'секс', 'секас', 'трах', 'ебу', 'выеб', 
    'выебыва', 'заеб', 'уеб', 'проеб', 'разъеб', 'съеб', 'выебен', 'ебанут', 'ебанат', 
    'ебись', 'ебёш', 'ебеш', 'ебло', 'еблив', 'ебстер', 'ебуч', 'ебуч', 'ебыр', 'ебыре', 
    'наеб', 'объеб', 'отъеб', 'перееб', 'подъеб', 'приеб', 'разъеб', 'съеб', 'уеб', 'пошел на',
    'админ','админка', 'администратор', 'разраб', 'разработчик', 'создатель', 
    
    'fuck', 'fucking', 'fucker', 'motherfucker', 'shit', 'bullshit', 'bitch', 'asshole', 
    'cunt', 'dick', 'pussy', 'nigger', 'nigga', 'whore', 'slut', 'bastard', 'douche', 
    'douchebag', 'faggot', 'retard', 'retarded', 'piss', 'pissed', 'damn', 'god damn', 
    'goddamn', 'hell', 'screw', 'screwed', 'sucks', 'sucker', 'wanker', 'tosser', 'twat', 
    'bellend', 'knob', 'prick', 'dickhead', 'arse', 'arsehole', 'bollocks', 'bugger', 
    'bloody', 'chink', 'spic', 'kike', 'wetback', 'cracker', 'redneck', 'white trash',
    'admin', 'administrator',
    
    'убейся', 'сдохни', 'помри', 'отъебис', 'отвали', 'пошёл на', 'иди на', 'поехал', 
    'псих', 'психическ', 'больной', 'спятил', 'чокнутый', 'ненормальн', 'куколд', 'рогоносец',
    'алкаш', 'алкоголик', 'наркоман', 'токсик', 'бездарь', 'неудачник', 'лузер', 'лох', 
    'лошара', 'черножоп', 'жид', 'хач', 'чурка', 'азер', 'армен', 'таджик', 'узбек', 
    'цыган', 'нигер', 'негр', 'черномазый', 'узкоглаз', 'китаез', 'япош', 'кореец',
    
    'жирный', 'жирная', 'жиродуб', 'толстый', 'толстая', 'дрыщ', 'дрищ', 'скелет', 
    'уродина', 'страшила', 'калек', 'инвалид', 'даунич', 'синдром', 'дауна',
    
    'убью', 'убить', 'зарежу', 'зарублю', 'застрелю', 'изобью', 'изнасилую', 'насилие',
    'издевательств', 'пытк', 'мучён', 'истязан'];

foreach ($forbidden_words as $word) {
    if (mb_stripos($name, $word) !== false) {
        echo json_encode(["success" => false, "message" => "Логин содержит недопустимые слова"]);
        exit;
    }
}

if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) {
    echo json_encode(["success" => false, "message" => "Логин может содержать только английские буквы и цифры без пробелов"]);
    exit;
}

if (mb_strlen($name) < 3 || mb_strlen($name) > 20) {
    echo json_encode(["success" => false, "message" => "Логин должен быть от 3 до 20 символов"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Введите корректный адрес почты"]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Пароль должен быть минимум 6 символов"]);
    exit;
}

$checkQuery = "SELECT login, email FROM users WHERE login = ? OR email = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($stmt, "ss", $name, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    if (mb_strtolower($row['login']) === mb_strtolower($name)) {
        echo json_encode(["success" => false, "message" => "Этот логин уже занят"]);
    } else if (mb_strtolower($row['email']) === mb_strtolower($email)) {
        echo json_encode(["success" => false, "message" => "Эта почта уже используется"]);
    }
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$image_id = rand(1, 10);

$insertQuery = "INSERT INTO users (login, nickname, email, password, image_id) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $insertQuery);

mysqli_stmt_bind_param($stmt, "ssssi", $name, $name, $email, $hashedPassword, $image_id);

if (mysqli_stmt_execute($stmt)) {
    $user_id = mysqli_insert_id($conn);

    $_SESSION["user"] = [
        "id" => $user_id,
        "login" => $name,
        "nickname" => $name,
        "email" => $email,
        "image_id" => $image_id
    ];

    echo json_encode([
        "success" => true,
        "message" => "Регистрация успешна",
        "user" => $_SESSION["user"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при записи в базу данных"]);
}