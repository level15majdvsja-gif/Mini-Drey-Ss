<?php

$branco = "\e[97m";
$preto = "\e[30m\e[1m";
$amarelo = "\e[93m";
$laranja = "\e[38;5;208m";
$azul   = "\e[34m";
$lazul  = "\e[36m";
$cln    = "\e[0m";
$verde  = "\e[92m";
$fverde = "\e[32m";
$vermelho    = "\e[91m";
$magenta = "\e[35m";
$azulbg = "\e[44m";
$lazulbg = "\e[106m";
$verdebg = "\e[42m";
$lverdebg = "\e[102m";
$amarelobg = "\e[43m";
$lamarelobg = "\e[103m";
$vermelhobg = "\e[101m";
$cinza = "\e[37m";
$ciano = "\e[36m";
$bold   = "\e[1m";
function keller_banner(){
  echo "\e[97m
  \e[97mKellerSS Android \e[36mFucking Cheaters\e[97m
  \e[90mdiscord.gg/allianceoficial\e[97m

  )       (     (          (     
  ( /(       )\ )  )\ )       )\ )  
  )\()) (   (()/( (()/(  (   (()/(  
  |((_)\  )\   /(_)) /(_)) )\   /(_)) 
  |_ ((_)((_) (_))  (_))  ((_) (_))   
  | |/ / | __|| |   | |   | __|| _ \  
  ' <  | _| | |__ | |__ | _| |   /  
  _|\_\ |___||____||____||___||_|_\  

  \e[36mCoded By: KellerSS | Credits: Sheik\e[0m
  \n";
}


echo $cln;

function atualizar()
{
    global $cln, $bold, $fverde, $vermelho, $azul;
    echo "\n" . $bold . $azul . "  ┌─ KELLERSS UPDATER\n" . $cln;
    echo $vermelho . "  ⟳ Atualizando, aguarde...\n\n" . $cln;
    system("git fetch origin && git reset --hard origin/master && git clean -f -d");
    echo $bold . $fverde . "  ✓ Atualização concluída! Reinicie o scanner\n" . $cln;
    exit;
}

function detectarBypassShell() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $branco, $cln, $verde, $ciano;
    
    $bypassDetectado = false;
    $totalVerificacoes = 0;
    $problemasEncontrados = 0;
    
    echo "\n";
    echo $bold . $ciano . "  ANÁLISE COMPLETA DE SEGURANÇA DO DISPOSITIVO\n";
    echo $bold . $ciano . "  ============================================\n\n" . $cln;

    echo $bold . $azul . "  ► [1] VERIFICANDO DISPOSITIVO CONECTADO\n";
    echo $bold . $azul . "  ---------------------------------------\n" . $cln;
    
    $devices = shell_exec('adb devices 2>&1');
    if ($devices === null || strpos($devices, 'device') === false || strpos($devices, 'unauthorized') !== false) {
        echo $bold . $vermelho . "  [✗] Nenhum dispositivo detectado ou sem autorização!\n" . $cln;
        return false;
    }
    
    $check = shell_exec('adb shell "ls /sdcard 2>&1"');
    if ($check !== null && strpos($check, 'Permission denied') !== false) {
        echo $bold . $vermelho . "  [✗] ADB sem permissões suficientes!\n" . $cln;
        return false;
    }
    
    echo $bold . $verde . "  ✓ Dispositivo conectado com permissões adequadas\n\n" . $cln;


    echo $bold . $azul . "  ► [2] VERIFICANDO ESTADO DE BOOT VERIFICADO\n";
    echo $bold . $azul . "  -------------------------------------------\n" . $cln;
    
    $verifiedBootState = trim(shell_exec('adb shell getprop ro.boot.verifiedbootstate 2>/dev/null'));
    
    if ($verifiedBootState === 'yellow') {
        echo $bold . $amarelo . "  ⚠ Boot State: YELLOW - Suspeita de modificação no sistema\n" . $cln;
        $bypassDetectado = true;
        $problemasEncontrados++;
    } elseif ($verifiedBootState === 'orange') {
        echo $bold . $vermelho . "  ✗ Boot State: ORANGE - Bootloader desbloqueado detectado\n" . $cln;
        $bypassDetectado = true;
        $problemasEncontrados++;
    } elseif ($verifiedBootState === 'green') {
        echo $bold . $verde . "  ✓ Boot State: GREEN - Sistema verificado\n" . $cln;
    } else {
        echo $bold . $amarelo . "  ⚠ Boot State: $verifiedBootState (Desconhecido)\n" . $cln;
    }
    $totalVerificacoes++;


    echo "\n" . $bold . $azul . "  ► [3] VERIFICANDO STATUS DO SELINUX\n";
    echo $bold . $azul . "  -----------------------------------\n" . $cln;
    
    $selinux = trim(shell_exec('adb shell getenforce 2>/dev/null'));
    
    if ($selinux === 'Permissive') {
        echo $bold . $vermelho . "  ✗ SELinux: PERMISSIVE - Modo permissivo detectado (comum em dispositivos rooteados)\n" . $cln;
        $bypassDetectado = true;
        $problemasEncontrados++;
    } elseif ($selinux === 'Enforcing') {
        echo $bold . $verde . "  ✓ SELinux: ENFORCING - Modo de segurança ativo\n" . $cln;
    } else {
        echo $bold . $amarelo . "  ⚠ SELinux: $selinux (Status desconhecido)\n" . $cln;
    }
    $totalVerificacoes++;


    echo "\n" . $bold . $azul . "  ► [4] VERIFICANDO PROPRIEDADES DO SISTEMA\n";
    echo $bold . $azul . "  -----------------------------------------\n" . $cln;
    
    $propriedadesSuspeitas = [
        'ro.debuggable' => ['valor' => '1', 'descricao' => 'Modo debug ativado'],
        'ro.secure' => ['valor' => '0', 'descricao' => 'Segurança desativada'],
        'service.adb.root' => ['valor' => '1', 'descricao' => 'ADB root ativo'],
        'ro.build.selinux' => ['valor' => '0', 'descricao' => 'SELinux desabilitado'],
        'ro.boot.flash.locked' => ['valor' => '0', 'descricao' => 'Flash desbloqueado'],
        'ro.boot.veritymode' => ['valor' => 'disabled', 'descricao' => 'dm-verity desabilitado'],
        'sys.oem_unlock_allowed' => ['valor' => '1', 'descricao' => 'OEM unlock permitido'],
        'persist.sys.usb.config' => ['valor' => 'adb', 'descricao' => 'ADB persistente ativo'],
        'ro.kernel.qemu' => ['valor' => '1', 'descricao' => 'Emulador detectado'],
    ];

    foreach ($propriedadesSuspeitas as $prop => $info) {
        $valor = trim(shell_exec("adb shell getprop $prop 2>/dev/null"));
        if ($valor === $info['valor']) {
            echo $bold . $vermelho . "  ✗ Propriedade suspeita: $prop = $valor ({$info['descricao']})\n" . $cln;
            $bypassDetectado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    echo $bold . $verde . "  ✓ Verificação de propriedades concluída\n" . $cln;


    echo "\n" . $bold . $azul . "  ► [5] VERIFICANDO BINÁRIOS SU (SUPERUSUÁRIO)\n";
    echo $bold . $azul . "  --------------------------------------------\n" . $cln;
    
    $binariosSU = [
        '/system/bin/su',
        '/system/xbin/su',
        '/sbin/su',
        '/system/su',
        '/system/bin/.ext/.su',
        '/data/local/su',
        '/data/local/bin/su',
        '/data/local/xbin/su',
        '/su/bin/su',
        '/system/sbin/su',
        '/vendor/bin/su',
        '/system/app/Superuser.apk',
        '/data/adb/magisk',
        '/data/adb/ksu', 
        '/data/adb/ap',   
        '/cache/su',
        '/dev/com.koushikdutta.superuser.daemon',
    ];
    
    $suEncontrado = false;
    foreach ($binariosSU as $bin) {
        $cmd = 'adb shell "test -f ' . escapeshellarg($bin) . ' && echo FOUND || echo NOTFOUND" 2>/dev/null';
        $result = trim(shell_exec($cmd) ?? '');
        if ($result === 'FOUND') {
            echo $bold . $vermelho . "  ✗ Binário SU encontrado: $bin\n" . $cln;
            $bypassDetectado = true;
            $suEncontrado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    if (!$suEncontrado) {
        echo $bold . $verde . "  ✓ Nenhum binário SU encontrado\n" . $cln;
    }


    echo "\n" . $bold . $azul . "  ► [6] DETECÇÃO AVANÇADA DE MAGISK\n";
    echo $bold . $azul . "  ---------------------------------\n" . $cln;
    
    $magiskDetectado = false;
    
    $magiskPkgs = shell_exec('adb shell "pm list packages 2>/dev/null | grep -iE \'magisk|topjohnwu\'"');
    if ($magiskPkgs && !empty(trim($magiskPkgs))) {
        echo $bold . $vermelho . "  ✗ Pacote Magisk encontrado:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($magiskPkgs) . "\n" . $cln;
        $bypassDetectado = true;
        $magiskDetectado = true;
        $problemasEncontrados++;
    }
    
    $magiskDirs = [
        '/data/adb/magisk',
        '/sbin/.magisk',
        '/data/adb/modules',
        '/cache/magisk.log'
    ];
    
    foreach ($magiskDirs as $dir) {
        $check = trim(shell_exec('adb shell "test -e ' . escapeshellarg($dir) . ' && echo FOUND || echo NOTFOUND" 2>/dev/null') ?? '');
        if ($check === 'FOUND') {
            echo $bold . $vermelho . "  ✗ Diretório/arquivo Magisk encontrado: $dir\n" . $cln;
            $bypassDetectado = true;
            $magiskDetectado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    $magiskProcs = shell_exec('adb shell "ps -A 2>/dev/null | grep -iE \'magisk|magiskd\'"');
    if ($magiskProcs && !empty(trim($magiskProcs))) {
        echo $bold . $vermelho . "  ✗ Processo Magisk em execução:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($magiskProcs) . "\n" . $cln;
        $bypassDetectado = true;
        $magiskDetectado = true;
        $problemasEncontrados++;
    }
    

    $magiskMounts = shell_exec('adb shell "mount 2>/dev/null | grep magisk"');
    if ($magiskMounts && !empty(trim($magiskMounts))) {
        echo $bold . $vermelho . "  ✗ Mountpoint Magisk detectado:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($magiskMounts) . "\n" . $cln;
        $bypassDetectado = true;
        $magiskDetectado = true;
        $problemasEncontrados++;
    }
    
    if (!$magiskDetectado) {
        echo $bold . $verde . "  ✓ Nenhum vestígio de Magisk encontrado\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [7] DETECÇÃO DE KERNELSU\n";
    echo $bold . $azul . "  --------------------------\n" . $cln;
    
    $kernelsuDetectado = false;
    
    $kernelMod = shell_exec('adb shell "lsmod 2>/dev/null | grep -i kernelsu"');
    if ($kernelMod && !empty(trim($kernelMod))) {
        echo $bold . $vermelho . "  ✗ Módulo KernelSU no kernel:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($kernelMod) . "\n" . $cln;
        $bypassDetectado = true;
        $kernelsuDetectado = true;
        $problemasEncontrados++;
    }
    
    $kernelsuFiles = [
        '/data/adb/ksud',
        '/data/adb/ksu',
        '/proc/kernelsu'
    ];
    
    foreach ($kernelsuFiles as $file) {
        $check = trim(shell_exec('adb shell "test -e ' . escapeshellarg($file) . ' && echo FOUND || echo NOTFOUND" 2>/dev/null') ?? '');
        if ($check === 'FOUND') {
            echo $bold . $vermelho . "  ✗ Arquivo/diretório KernelSU encontrado: $file\n" . $cln;
            $bypassDetectado = true;
            $kernelsuDetectado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    $kernelVersion = shell_exec('adb shell "uname -r 2>/dev/null | grep -i ksu"');
    if ($kernelVersion && !empty(trim($kernelVersion))) {
        echo $bold . $vermelho . "  ✗ Kernel modificado com KernelSU:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($kernelVersion) . "\n" . $cln;
        $bypassDetectado = true;
        $kernelsuDetectado = true;
        $problemasEncontrados++;
    }
    
    if (!$kernelsuDetectado) {
        echo $bold . $verde . "  ✓ Nenhum vestígio de KernelSU encontrado\n" . $cln;
    }


    echo "\n" . $bold . $azul . "  ► [8] DETECÇÃO DE APATCH\n";
    echo $bold . $azul . "  ------------------------\n" . $cln;
    
    $apatchDetectado = false;
    
    $apatchPkgs = shell_exec('adb shell "pm list packages 2>/dev/null | grep -i apatch"');
    if ($apatchPkgs && !empty(trim($apatchPkgs))) {
        echo $bold . $vermelho . "  ✗ Pacote APatch encontrado:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($apatchPkgs) . "\n" . $cln;
        $bypassDetectado = true;
        $apatchDetectado = true;
        $problemasEncontrados++;
    }
    
    $apatchDir = trim(shell_exec('adb shell "test -d /data/adb/ap && echo FOUND || echo NOTFOUND" 2>/dev/null') ?? '');
    if ($apatchDir === 'FOUND') {
        echo $bold . $vermelho . "  ✗ Diretório APatch encontrado: /data/adb/ap\n" . $cln;
        $bypassDetectado = true;
        $apatchDetectado = true;
        $problemasEncontrados++;
    }
    
    $apatchProp = shell_exec('adb shell "getprop 2>/dev/null | grep -i apatch"');
    if ($apatchProp && !empty(trim($apatchProp))) {
        echo $bold . $vermelho . "  ✗ Propriedade APatch encontrada:\n" . $cln;
        echo $bold . $amarelo . "    " . trim($apatchProp) . "\n" . $cln;
        $bypassDetectado = true;
        $apatchDetectado = true;
        $problemasEncontrados++;
    }
    
    if (!$apatchDetectado) {
        echo $bold . $verde . "  ✓ Nenhum vestígio de APatch encontrado\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [9] ANÁLISE DE LOGS DO KERNEL E SISTEMA\n";
    echo $bold . $azul . "  -----------------------------------------\n" . $cln;
    
    $logChecks = [
        'Logcat Kernel' => 'adb shell "logcat -b kernel -d 2>/dev/null | grep -iE \'kernelsu|magisk|apatch\'"',
        'Dumpsys Package' => 'adb shell "dumpsys package 2>/dev/null | grep -iE \'kernelsu|magisk|apatch\' | grep -v queriesPackages | grep -vE \'KernelSupport|Freecess|ChinaPolicy\' | grep -v \"used by other apps\""',
        'Dumpsys Activity' => 'adb shell "dumpsys activity 2>/dev/null | grep -iE \'kernelsu|magisk|apatch\' | grep -v queriesPackages | grep -vE \'KernelSupport|Freecess|ChinaPolicy\' | grep -v \"used by other apps\""',
        'Dumpsys Processes' => 'adb shell "dumpsys activity processes 2>/dev/null | grep -iE \'kernelsu|magisk|apatch\'"'
    ];

    $logDetectado = false;
    foreach ($logChecks as $checkName => $cmd) {
        $output = shell_exec($cmd);
        if ($output && !empty(trim($output))) {
            echo $bold . $vermelho . "  ✗ Root detectado em $checkName:\n" . $cln;
            echo $bold . $amarelo . "    " . substr(trim($output), 0, 200) . "...\n" . $cln;
            $bypassDetectado = true;
            $logDetectado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    if (!$logDetectado) {
        echo $bold . $verde . "  ✓ Logs do sistema limpos\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [10] DETECÇÃO DE FRAMEWORKS DE HOOK\n";
    echo $bold . $azul . "  -------------------------------------\n" . $cln;
    
    $hookFrameworks = [
        'Xposed' => [
            'adb shell "pm list packages 2>/dev/null | grep -iE \'xposed|exposed\'"',
            'adb shell "test -f /system/framework/XposedBridge.jar && echo FOUND || echo NOTFOUND"'
        ],
        'LSPosed' => [
            'adb shell "pm list packages 2>/dev/null | grep -i lsposed"',
            'adb shell "test -d /data/adb/lspd && echo FOUND || echo NOTFOUND"'
        ],
        'EdXposed' => [
            'adb shell "pm list packages 2>/dev/null | grep -i edxposed"'
        ],
        'Frida' => [
            'adb shell "ps -A 2>/dev/null | grep frida"',
            'adb shell "netstat -tunlp 2>/dev/null | grep 27042 | grep -E \"LISTEN|ESTABLISHED\""'
        ],
        'Substrate' => [
            'adb shell "pm list packages 2>/dev/null | grep -i substrate"'
        ]
    ];

    $hookDetectado = false;
    foreach ($hookFrameworks as $framework => $checks) {
        foreach ($checks as $check) {
            $output = shell_exec($check);
            $outputTrim = trim($output ?? '');

            $encontrado = false;
            
            if (!empty($outputTrim)) {
                if (strpos($check, 'FOUND') !== false) {
        
                    if ($outputTrim === 'FOUND') {
                        $encontrado = true;
                    }
                } else {

                    $encontrado = true;
                }
            }
            
            if ($encontrado) {
                echo $bold . $vermelho . "  ✗ Framework de hook detectado: $framework\n" . $cln;
                echo $bold . $amarelo . "    Detalhes: " . substr($outputTrim, 0, 100) . "\n" . $cln;
                $bypassDetectado = true;
                $hookDetectado = true;
                $problemasEncontrados++;
                break;
            }
            $totalVerificacoes++;
        }
    }
    
    if (!$hookDetectado) {
        echo $bold . $verde . "  ✓ Nenhum framework de hook detectado\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [11] VERIFICANDO FUNÇÕES SHELL SOBRESCRITAS\n";
    echo $bold . $azul . "  ---------------------------------------------\n" . $cln;
    
    $funcoesTeste = [
        'pkg' => 'adb shell "type pkg 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'git' => 'adb shell "type git 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"', 
        'cd' => 'adb shell "type cd 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'stat' => 'adb shell "type stat 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'adb' => 'adb shell "type adb 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'ls' => 'adb shell "type ls 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'cat' => 'adb shell "type cat 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'pm' => 'adb shell "type pm 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"'
    ];
    
    $funcaoSobrescrita = false;
    foreach ($funcoesTeste as $funcao => $comando) {
        $resultado = shell_exec($comando);
        if ($resultado !== null && strpos($resultado, 'FUNCTION_DETECTED') !== false) {
            echo $bold . $vermelho . "  ✗ BYPASS DETECTADO: Função '$funcao' foi sobrescrita!\n" . $cln;
            $bypassDetectado = true;
            $funcaoSobrescrita = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    if (!$funcaoSobrescrita) {
        echo $bold . $verde . "  ✓ Todas as funções shell estão normais\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [12] TESTANDO ACESSO A DIRETÓRIOS CRÍTICOS\n";
    echo $bold . $azul . "  --------------------------------------------\n" . $cln;
    
    $diretoriosCriticos = [
        '/system/bin' => 'Binários do sistema',
        '/data/data/com.dts.freefireth/files' => 'Dados Free Fire TH',
        '/data/data/com.dts.freefiremax/files' => 'Dados Free Fire MAX',
        '/storage/emulated/0/Android/data' => 'Dados de aplicativos',
        '/data/adb' => 'Diretório ADB',
        '/system/xbin' => 'Binários estendidos'
    ];
    
    $acessoBloqueado = false;
    foreach ($diretoriosCriticos as $diretorio => $descricao) {
        $comandoTestDir = 'adb shell "ls -la \"' . $diretorio . '\" 2>&1 | head -3"';
        $resultadoTestDir = shell_exec($comandoTestDir);
        
        if (empty($resultadoTestDir) || trim($resultadoTestDir ?? '') === '') {
            echo $bold . $amarelo . "  ⚠ Sem resposta do diretório: $diretorio ($descricao)\n" . $cln;
        } elseif (($resultadoTestDir !== null && strpos($resultadoTestDir, 'blocked') !== false) ||
                  ($resultadoTestDir !== null && strpos($resultadoTestDir, 'redirected') !== false) ||
                  ($resultadoTestDir !== null && strpos($resultadoTestDir, 'bypass') !== false)) {
            
            echo $bold . $vermelho . "  ✗ BYPASS DETECTADO: Acesso bloqueado/redirecionado\n" . $cln;
            echo $bold . $amarelo . "    Diretório: $diretorio ($descricao)\n" . $cln;
            echo $bold . $amarelo . "    Resposta: " . substr(trim($resultadoTestDir ?? ''), 0, 100) . "\n" . $cln;
            $bypassDetectado = true;
            $acessoBloqueado = true;
            $problemasEncontrados++;
        }
        $totalVerificacoes++;
    }
    
    if (!$acessoBloqueado) {
        echo $bold . $verde . "  ✓ Acesso aos diretórios está normal\n" . $cln;
    }

    echo "\n" . $bold . $azul . "  ► [13] VERIFICANDO PROCESSOS SUSPEITOS\n";
    echo $bold . $azul . "  -------------
