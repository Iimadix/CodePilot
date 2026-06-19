<?php
require_once "header_helper.php";
require_once "../config/connect.php";

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "message" => "Доступ запрещен"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$userId = $_SESSION['user']['id'];
$newNickname = trim($data["nickname"] ?? '');
$newLevel = trim($data["level"] ?? 'Стажер');
$newTech = trim($data["tech_stack"] ?? '');
$newCountry = trim($data["country"] ?? '');
$newCity = trim($data["city"] ?? '');
$newBio = trim($data["bio"] ?? '');

if (empty($newNickname)) {
    echo json_encode(["success" => false, "message" => "Никнейм не может быть пустым"]);
    exit;
}

$checkLogin = mysqli_prepare($conn, "SELECT id FROM users WHERE login = ? AND id != ?");
mysqli_stmt_bind_param($checkLogin, "si", $newLogin, $userId);
mysqli_stmt_execute($checkLogin);
if (mysqli_num_rows(mysqli_stmt_get_result($checkLogin)) > 0) {
    echo json_encode(["success" => false, "message" => "Этот логин уже занят другим пользователем"]);
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

if (!preg_match('/^[\p{L}\p{N}]+$/u', $newNickname)) {
    echo json_encode([
        "success" => false, 
        "message" => "Никнейм может содержать только буквы и цифры без пробелов и знаков"
    ]);
    exit;
}
    function hasBadWords($text, $words) {
    foreach ($words as $word) {
        if (mb_stripos($text, $word) !== false) return true;
    }
    return false;
}

if (hasBadWords($newNickname, $forbidden_words) || 
    hasBadWords($newBio, $forbidden_words) ||
    hasBadWords($newTech, $forbidden_words)) { 
    
    echo json_encode(["success" => false, "message" => "Данные содержат недопустимые слова"]);
    exit;
}

$query = "UPDATE users SET nickname = ?, level = ?, tech_stack = ?, country = ?, city = ?, bio = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "ssssssi", $newNickname, $newLevel, $newTech, $newCountry, $newCity, $newBio, $userId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['user']['nickname'] = $newNickname;
    $_SESSION['user']['level'] = $newLevel;
    $_SESSION['user']['tech_stack'] = $newTech;
    $_SESSION['user']['bio'] = $newBio;

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при сохранении данных в БД"]);
}