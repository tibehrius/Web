<?php
/**************************************************************************
 * Este programa é SourceBans parte ++.
 *
 * Todos os direitos reservados © 2014-2016 Sarabveer Singh <me@sarabveer.me>
 *
 * SourceBans ++ está licenciado sob
 * Atribuição-UsoNãoComercial ShareAlike- 3.0.
 *
 * Você deve ter recebido uma cópia da Licença em conjunto com este trabalho. Se não,
 * Sé. <Http://creativecommons.org/licenses/by-nc-sa/3.0/>.
 *
 * O SOFTWARE É FORNECIDO "COMO ESTÁ", SEM QUALQUER
 * GARANTIAS, expressa ou implícita, incluindo, mas não limitado a,
 * ADEQUAÇÃO A UM DETERMINADO FIM E NÃO VIOLAÇÃO. EM HIPÓTESE
 * CASO OS DETENTORES DOS DIREITOS AUTORAIS OU SEUS COLABORADORES SER RESPONSABILIZADOS POR
 * Qualquer reclamação ou DANOS, SEJA EM UMA AÇÃO DE CONTRATO,
 * DELITO OU DE OUTRA FORMA, DECORRENTES DE OU EM CONEXÃO COM
 * Software ou o uso ou outras negociações
 * SOFTWARE.
 *
 * Este programa é baseado na obra coberta com o seguinte copyright
 * O direito (s):
 *
 * * SourceBans 1.4.11
 * Direitos de autor © 2007-2014 SourceBans Team - Parte GameConnect
 * Liberado sob licença CC BY-NC-SA 3,0
 * Page: <http://www.sourcebans.net/> - <http://www.gameconnect.net/>
 *
 * * SourceBans TF2 Tema v1.0
 * Direitos de autor © 2014 IceMan
 * Page: <https://forums.alliedmods.net/showthread.php?t=252533>
 *
 ***************************************************************************/

if (!defined('IN_SB')) {echo("You should not be here. Only follow links!");die();}

class CDonate {
    private $hooks = array();
    
    /**
     * Add tariff
     *
     * @return int
     */
    public function AddTariff($name, $price, $expired, $desc, $webflags, $serverflags, $immunity, $servers) {
        $query = sprintf("INSERT INTO `%s_billing_admintariffs` (`name`, `price`, `expired`, `desc`, `webflags`, `serverflags`, `immunity`, `servers`) VALUES (%s, %d, %d, %s, %s, %s, %d, %s)", DB_PREFIX, $GLOBALS['db']->qstr($name), $price, $expired, $GLOBALS['db']->qstr($desc), $GLOBALS['db']->qstr($webflags), $GLOBALS['db']->qstr($serverflags), $immunity, $GLOBALS['db']->qstr($servers));
        $GLOBALS['db']->Execute($query);
        return $GLOBALS['db']->Insert_ID();
    }
    
    /**
     * Add admin request payment
     *
     * @return int
     */
    public function AddPayment_Admin($name, $authid, $tariff, $vk = '', $skype = '') {
        if (!$this->IsTariffExists($tariff))
            return -1;
        
        $query = sprintf("INSERT INTO `%s_billing_adminpayments` (`name`, `authid`, `tariff`, `vk`, `skype`) VALUES (%s, %s, %d, %s, %s);", DB_PREFIX, $GLOBALS['db']->qstr($name), $GLOBALS['db']->qstr($authid), (int) $tariff, $GLOBALS['db']->qstr($vk), $GLOBALS['db']->qstr($skype));
        $GLOBALS['db']->Execute($query);
        return $GLOBALS['db']->Insert_ID();
    }
    
    /**
     * Add unban request payment
     *
     * @return int
     */
    public function AddPayment_Unban($banid) {
        // IN DEVELOPING
    }
    
    // HELPERS //
    /**
     * Get client IP
     *
     * @return string ClientIP
     */
    public static function getIP() {
        return $_SERVER[isset($_SERVER['HTTP_X_REAL_IP'])?'HTTP_X_REAL_IP':'REMOTE_ADDR'];
    }
    
    /**
     * Checks tariff on exists.
     *
     * @return bool
     */
    public static function IsTariffExists($id) {
        return $GLOBALS['db']->GetOne(sprintf("SELECT COUNT(*) FROM `%s_billing_admintariffs` WHERE `id` = %d;", DB_PREFIX, (int) $id)) == 1;
    }
    
    /**
     * Register event hook.
     *
     * @noreturn
     */
    public function registerEvent($event_name, $func) {
        $this->hooks[$event_name][] = $func;
    }
    
    /** 
     * Fires a event for donate submodules
     *
     * @noreturn
     */
    public function fireEvent($event_name, $data) {
        if (!isset($this->hooks[$event_name]))
            return;
        
        foreach ($this->hooks[$event_name] as $event_handler) {
            call_user_func_array($event_handler, $data);
        }
    }
}

// This is skeleton for custom user payment services. DO NOT EDIT THIS.
class CPaymentService {
    /**
     * Returns the name of this SourceBans Payment Service.
     *
     * @return string Service name
     */
    public function getName() {}
    
    /**
     * Returns the author name. Allowed HTML chars.
     *
     * @return string Author Name
     */
    public function getAuthor() {}
    
    /**
     * Returns the version.
     *
     * @return string Version
     */
    public function getVersion() {}
    
    /**
     * Returns the provider WebSite.
     *
     * @return string Provider site
     */
    public function getUrl() {}
    
    /**
     * Generate client sign.
     * 
     * @return string ClientSign
     */
    public function getClientSign() {}
    
    /**
     * Generate notification sign.
     *
     * @return string NotifySign
     */
    public function getNotifySign() {}
    
    /**
     * Generate URL for client redirect.
     *
     * @return string URL.
     */
    public function generatePaymentUrl() {}
}
