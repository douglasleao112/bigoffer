<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// 1. Verificação de BOT do Facebook
$facebookBots = ['facebookexternalhit', 'Facebot', 'facebookcatalog'];
$isBot = false;
foreach ($facebookBots as $bot) {
    if (stripos($userAgent, $bot) !== false) {
        $isBot = true;
        break;
    }
}

// 2. Detecta cidade pelo IP (usando ip-api.com)
function getCity($ip) {
    $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=city");
    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data['city'])) {
            return strtolower(trim($data['city']));
        }
    }
    return '';
}

$city = getCity($ip);

// 3. Lista de cidades bloqueadas
$blockedCities = ['goiânia', 'goiania', 'aparecida de goiânia', 'aparecida', 'aparecida de goiania'];

// 4. Detecta se é celular
$isMobile = preg_match('/(android|iphone|ipad|ipod|blackberry|windows phone)/i', $userAgent);

// 5. LOG para auditoria
@file_put_contents("log_acesso.txt", date('Y-m-d H:i:s') . " | IP: $ip | Cidade: $city | Bot: " . ($isBot ? 'Sim' : 'Não') . " | Mobile: " . ($isMobile ? 'Sim' : 'Não') . "\n", FILE_APPEND);

// 6. Captura UTMs (se existirem) e repassa no redirecionamento
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$utm = $queryString ? "?$queryString" : "";

// 7. Regras de redirecionamento
if (
    $isBot ||                                 // Regra 1: Bot do Facebook
    !$isMobile ||                             // Regra 2: Desktop (não mobile)
    in_array($city, $blockedCities)           // Regra 3: Goiânia ou Aparecida
) {
    header("Location: https://sun.eduzz.com/E0D6P4V691/$utm"); // Página fria
    exit;
}

// 8. Caso contrário: é mobile, fora das cidades bloqueadas e não é bot
header("Location: https://bigoffer.netlify.app/lp-1/$utm"); // Página quente
exit;
?>
