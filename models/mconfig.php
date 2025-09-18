<?php
require_once 'conexion.php';

class mconfig {
    // Atributos
    private $id_cfg;
    private $moneda;
    private $idioma;
    private $zona_hor;
    private $fmt_fecha;
    private $estilo_ui;
    private $act_auto;
    private $notif_act;
    private $act_prog;
    private $auth_2fa;
    private $intentos_max;
    private $bloqueo_min;
    private $log_cambios;
    private $retencion_log;
    private $id_usu_mod;
    private $fch_ult_mod;

    // Conexion
    private $db;

    public function __construct() {
        $conexion = new conexion();
        $this->db = $conexion->get_conexion();
    }

    // Getters
    public function getIdCfg() {
        return $this->id_cfg;
    }

    public function getMoneda() {
        return $this->moneda;
    }

    public function getIdioma() {
        return $this->idioma;
    }

    public function getZonaHor() {
        return $this->zona_hor;
    }

    public function getFmtFecha() {
        return $this->fmt_fecha;
    }

    public function getEstiloUi() {
        return $this->estilo_ui;
    }

    public function getActAuto() {
        return $this->act_auto;
    }

    public function getNotifAct() {
        return $this->notif_act;
    }

    public function getActProg() {
        return $this->act_prog;
    }

    public function getAuth2fa() {
        return $this->auth_2fa;
    }

    public function getIntentosMax() {
        return $this->intentos_max;
    }

    public function getBloqueoMin() {
        return $this->bloqueo_min;
    }

    public function getLogCambios() {
        return $this->log_cambios;
    }

    public function getRetencionLog() {
        return $this->retencion_log;
    }

    public function getIdUsuMod() {
        return $this->id_usu_mod;
    }

    public function getFchUltMod() {
        return $this->fch_ult_mod;
    }

    // Setters
    public function setIdCfg($id_cfg) {
        $this->id_cfg = $id_cfg;
        return $this;
    }

    public function setMoneda($moneda) {
        $this->moneda = $moneda;
        return $this;
    }

    public function setIdioma($idioma) {
        $this->idioma = $idioma;
        return $this;
    }

    public function setZonaHor($zona_hor) {
        $this->zona_hor = $zona_hor;
        return $this;
    }

    public function setFmtFecha($fmt_fecha) {
        $this->fmt_fecha = $fmt_fecha;
        return $this;
    }

    public function setEstiloUi($estilo_ui) {
        $this->estilo_ui = $estilo_ui;
        return $this;
    }

    public function setActAuto($act_auto) {
        $this->act_auto = $act_auto;
        return $this;
    }

    public function setNotifAct($notif_act) {
        $this->notif_act = $notif_act;
        return $this;
    }

    public function setActProg($act_prog) {
        $this->act_prog = $act_prog;
        return $this;
    }

    public function setAuth2fa($auth_2fa) {
        $this->auth_2fa = $auth_2fa;
        return $this;
    }

    public function setIntentosMax($intentos_max) {
        $this->intentos_max = $intentos_max;
        return $this;
    }

    public function setBloqueoMin($bloqueo_min) {
        $this->bloqueo_min = $bloqueo_min;
        return $this;
    }

    public function setLogCambios($log_cambios) {
        $this->log_cambios = $log_cambios;
        return $this;
    }

    public function setRetencionLog($retencion_log) {
        $this->retencion_log = $retencion_log;
        return $this;
    }

    public function setIdUsuMod($id_usu_mod) {
        $this->id_usu_mod = $id_usu_mod;
        return $this;
    }

    public function setFchUltMod($fch_ult_mod) {
        $this->fch_ult_mod = $fch_ult_mod;
        return $this;
    }

    // Métodos
    public function getAll() {
        $query = $this->db->query("SELECT * FROM cfg_sis");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $query = $this->db->prepare("SELECT * FROM cfg_sis WHERE id_cfg = :id");
        $query->execute([':id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Método para obtener la configuración actual
    public function getCurrentConfig() {
        $query = $this->db->query("SELECT * FROM cfg_sis LIMIT 1");
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Método para guardar una nueva configuración
    public function save() {
        $sql = "INSERT INTO cfg_sis (
            moneda, idioma, zona_hor, fmt_fecha, estilo_ui, act_auto, 
            notif_act, act_prog, auth_2fa, intentos_max, bloqueo_min, 
            log_cambios, retencion_log, id_usu_mod
        ) VALUES (
            :moneda, :idioma, :zona_hor, :fmt_fecha, :estilo_ui, :act_auto, 
            :notif_act, :act_prog, :auth_2fa, :intentos_max, :bloqueo_min, 
            :log_cambios, :retencion_log, :id_usu_mod
        )";
        
        $query = $this->db->prepare($sql);
        $result = $query->execute([
            ':moneda' => $this->moneda,
            ':idioma' => $this->idioma,
            ':zona_hor' => $this->zona_hor,
            ':fmt_fecha' => $this->fmt_fecha,
            ':estilo_ui' => $this->estilo_ui,
            ':act_auto' => $this->act_auto,
            ':notif_act' => $this->notif_act,
            ':act_prog' => $this->act_prog,
            ':auth_2fa' => $this->auth_2fa,
            ':intentos_max' => $this->intentos_max,
            ':bloqueo_min' => $this->bloqueo_min,
            ':log_cambios' => $this->log_cambios,
            ':retencion_log' => $this->retencion_log,
            ':id_usu_mod' => $this->id_usu_mod
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }

    // Método para actualizar una configuración existente
    public function update() {
        $sql = "UPDATE cfg_sis SET 
            moneda = :moneda,
            idioma = :idioma,
            zona_hor = :zona_hor,
            fmt_fecha = :fmt_fecha,
            estilo_ui = :estilo_ui,
            act_auto = :act_auto,
            notif_act = :notif_act,
            act_prog = :act_prog,
            auth_2fa = :auth_2fa,
            intentos_max = :intentos_max,
            bloqueo_min = :bloqueo_min,
            log_cambios = :log_cambios,
            retencion_log = :retencion_log,
            id_usu_mod = :id_usu_mod,
            fch_ult_mod = CURRENT_TIMESTAMP
        WHERE id_cfg = :id_cfg";
        
        $query = $this->db->prepare($sql);
        return $query->execute([
            ':moneda' => $this->moneda,
            ':idioma' => $this->idioma,
            ':zona_hor' => $this->zona_hor,
            ':fmt_fecha' => $this->fmt_fecha,
            ':estilo_ui' => $this->estilo_ui,
            ':act_auto' => $this->act_auto,
            ':notif_act' => $this->notif_act,
            ':act_prog' => $this->act_prog,
            ':auth_2fa' => $this->auth_2fa,
            ':intentos_max' => $this->intentos_max,
            ':bloqueo_min' => $this->bloqueo_min,
            ':log_cambios' => $this->log_cambios,
            ':retencion_log' => $this->retencion_log,
            ':id_usu_mod' => $this->id_usu_mod,
            ':id_cfg' => $this->id_cfg
        ]);
    }

    // Método para eliminar una configuración
    public function delete() {
        $query = $this->db->prepare("DELETE FROM cfg_sis WHERE id_cfg = :id");
        return $query->execute([':id' => $this->id_cfg]);
    }

    // Método para cargar los valores desde un array
    public function loadFromArray($data) {
        $this->setIdCfg($data['id_cfg'] ?? null);
        $this->setMoneda($data['moneda'] ?? 'COP');
        $this->setIdioma($data['idioma'] ?? 'Español');
        $this->setZonaHor($data['zona_hor'] ?? 'America/Bogota');
        $this->setFmtFecha($data['fmt_fecha'] ?? 'dd/mm/yyyy');
        $this->setEstiloUi($data['estilo_ui'] ?? 'Claro');
        $this->setActAuto(isset($data['act_auto']) ? 1 : 0);
        $this->setNotifAct(isset($data['notif_act']) ? 1 : 0);
        $this->setActProg($data['act_prog'] ?? null);
        $this->setAuth2fa(isset($data['auth_2fa']) ? 1 : 0);
        $this->setIntentosMax($data['intentos_max'] ?? 3);
        $this->setBloqueoMin($data['bloqueo_min'] ?? 30);
        $this->setLogCambios(isset($data['log_cambios']) ? 1 : 0);
        $this->setRetencionLog($data['retencion_log'] ?? 365);
        $this->setIdUsuMod($data['id_usu_mod'] ?? null);
        $this->setFchUltMod($data['fch_ult_mod'] ?? null);
    }
}
?>