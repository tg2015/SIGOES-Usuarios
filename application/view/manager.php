<?php
/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * Main UI Controller
 *
 * @package AAM
 * @author Vasyl Martyniuk <support@wpaam.com>
 * @copyright Copyright C Vasyl Martyniuk
 * @license GNU General Public License {@link http://www.gnu.org/licenses/}
 */
class aam_View_Manager extends aam_View_Abstract {

    /**
     * Admin Menu Feature
     */
    const FEATURE_ADMIN_MENU = 'admin_menu';

    /**
     * Metaboxes & Widgetst Feature
     */
    const FEATURE_METABOX = 'metabox';

    /**
     * Capability Feature
     */
    const FEATURE_CAPABILITY = 'capability';

    /**
     * Post Access Feature
     */
    const FEATURE_POST_ACCESS = 'post_access';

    /**
     * Event Manager Feature
     */
    const FEATURE_EVENT_MANAGER = 'event_manager';

    /**
     * Default ajax response
     */
    const DEFAULT_AJAX_RESPONSE = -1;
    
    /**
     * Constructor
     *
     * The filter "aam_cpanel" can be used to control the Control Panel items.
     *
     * @return void
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();

        $this->registerDefaultSubjects();
        $this->registerDefaultFeatures();
    }

    /**
     * Registet default list of subjects
     *
     * @return void
     *
     * @access protected
     */
    protected function registerDefaultSubjects() {
        aam_View_Collection::registerSubject((object)array(
            'position' => 5,
            'segment' => aam_Control_Subject_Role::UID,
            'label' => __('Roles', 'aam'),
            'title' => __('Roles', 'aam'),
            'class' => 'manager-item manager-item-role',
            'uid' => 'role',
            'controller' => 'aam_View_Role'
        ));

        
        aam_View_Collection::registerSubject((object)array(
            'position' => 10,
            'segment' => aam_Control_Subject_User::UID,
            'label' => __('Usuarios', 'aam'),
            'title' => __('Usuarios', 'aam'),
            'class' => 'manager-item manager-item-user',
            'uid' => 'user',
            'controller' => 'aam_View_User'
        ));

    }

    /**
     * Prepare default list of features
     *
     * Check if current user has proper capability to use the feature
     *
     * @return array
     *
     * @access protected
     */
    protected function registerDefaultFeatures() {
        $features = array();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////// ** ocultar menus feature aam         

        //Main Menu Tab
        aam_View_Collection::registerFeature((object)array(
            'uid' => self::FEATURE_ADMIN_MENU,
            'position' => 5,
            'title' => __('Menu Administracion', 'aam'),
            'subjects' => array(
                aam_Control_Subject_Role::UID, aam_Control_Subject_User::UID
            ),
            'controller' => 'aam_View_Menu'
        ));

       
        //Capability Tab
        aam_View_Collection::registerFeature((object)array(
            'uid' => self::FEATURE_CAPABILITY,
            'position' => 15,
            'title' => __('Capacidades', 'aam'),
            'subjects' => array(
                aam_Control_Subject_Role::UID, aam_Control_Subject_User::UID
            ),
            'controller' => 'aam_View_Capability'
        ));

       
        return $features;
    }

    /**
     * Run the Manager
     *
     * @return string
     *
     * @access public
     */
    public function run() {
        return $this->loadTemplate(dirname(__FILE__) . '/tmpl/manager.phtml');
    }

    /**
     * Process the ajax call
     *
     * @return string
     *
     * @access public
     */
    public function processAjax(){   
        $sub_method = aam_Core_Request::request('sub_action');

        if (method_exists($this, $sub_method)) {
            $response = call_user_func(array($this, $sub_method));
        } else {
            $response = apply_filters(
                'aam_ajax_call', self::DEFAULT_AJAX_RESPONSE, $this->getSubject()
            );
        }

        return $response;
    }

    /**
     * Render the Main Control Area
     *
     * @return void
     *
     * @access public
     */
    public function retrieveFeatures() {
        $features = aam_View_Collection::retriveFeatures($this->getSubject());
        if (count($features)){
        ?>
            <div class="feature-list">
                <?php
                foreach ($features as $feature) {
                    echo '<div class="feature-item" feature="' . $feature->uid . '">';
                    echo '<span>' . $feature->title . '</span></div>';
                }
                ?>
            </div>
            <div class="feature-content">
                <?php
                foreach ($features as $feature) {
                    echo $feature->controller->content($this->getSubject());
                }
                ?>
            </div>
            <br class="clear" />
        <?php
        } else {
            echo '<p class="feature-list-empty">';
            echo __('You are not allowed to manage any AAM Features.', 'aam');
            echo '</p>';
        }
        do_action('aam_post_features_render');
    }

    /**
     * Load List of Metaboxes
     *
     * @return string
     *
     * @access public
     */
    public function loadMetaboxes(){
        if (aam_View_Collection::hasFeature(self::FEATURE_METABOX)){
            $metabox = new aam_View_Metabox;
            $response = $metabox->retrieveList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Initialize list of metaboxes from individual link
     *
     * @return string
     *
     * @access public
     */
    public function initLink(){
        if (aam_View_Collection::hasFeature(self::FEATURE_METABOX)){
            $metabox = new aam_View_Metabox;
            $response = $metabox->initLink();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Retrieve Available for Editing Role List
     *
     * @return string
     *
     * @access public
     */
    public function roleList(){
        if (aam_View_Collection::hasSubject(aam_Control_Subject_Role::UID)){
            $role = new aam_View_Role;
            $response = $role->retrieveList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Retrieve Pure Role List
     *
     * @return string
     *
     * @access public
     */
    public function plainRoleList(){
        if (aam_View_Collection::hasSubject(aam_Control_Subject_Role::UID)){
            $role = new aam_View_Role;
            $response = $role->retrievePureList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Add New Role
     *
     * @return string
     *
     * @access public
     */
    public function addRole()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_Role::UID)) {
            $role = new aam_View_Role;
            $response = $role->add();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Edit Existing Role
     *
     * @return string
     *
     * @access public
     */
    public function editRole()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_Role::UID)) {
            $role = new aam_View_Role;
            $response = $role->edit();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Delete Existing Role
     *
     * @return string
     *
     * @access public
     */
    public function deleteRole()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_Role::UID)) {
            $role = new aam_View_Role;
            $response = $role->delete();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Retrieve Available User List
     *
     * @return string
     *
     * @access public
     */
    public function userList()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_User::UID)) {
            $user = new aam_View_User;
            $response = $user->retrieveList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Block Selected User
     *
     * @return string
     *
     * @access public
     */
    public function blockUser()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_User::UID)) {
            $user = new aam_View_User;
            $response = $user->block();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Delete Selected User
     *
     * @return string
     *
     * @access public
     */
    public function deleteList()
    {
        if (aam_View_Collection::hasSubject(aam_Control_Subject_User::UID)) {
            $user = new aam_View_User;
            $response = $user->delete();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Load list of capabilities
     *
     * @return string
     *
     * @access public
     */
    public function loadCapabilities()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_CAPABILITY)){
            $capability = new aam_View_Capability;
            $response = $capability->retrieveList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Get list of Capabilities by selected Role
     *
     * @return string
     *
     * @access public
     */
    public function roleCapabilities()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_CAPABILITY)){
            $capability = new aam_View_Capability;
            $response = $capability->retrieveRoleCapabilities();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Add New Capability
     *
     * @return string
     *
     * @access public
     */
    public function addCapability()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_CAPABILITY)){
            $capability = new aam_View_Capability;
            $response = $capability->addCapability();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Delete Capability
     *
     * @return string
     *
     * @access protected
     */
    public function deleteCapability()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_CAPABILITY)){
            $capability = new aam_View_Capability;
            $response = $capability->deleteCapability();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Restore Capabilities
     *
     * @return string
     *
     * @access public
     */
    public function restoreCapabilities()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_CAPABILITY)){
            $capability = new aam_View_Capability;
            $response = $capability->restoreCapability();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Get the List of Posts
     *
     * @return string
     *
     * @access public
     */
    public function postList()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->retrievePostList();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Get Post Tree
     *
     * @return string
     *
     * @access public
     */
    public function postTree()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->getPostTree();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Save Access settings for Post or Term
     *
     * @return string
     *
     * @access public
     */
    public function saveAccess()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->saveAccess();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Get Access settings for Post or Term
     *
     * @return string
     *
     * @access public
     */
    public function getAccess()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->getAccess();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Restore default access level for Post or Term
     *
     * @return string
     *
     * @access public
     */
    public function clearAccess()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->clearAccess();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Delete Post
     *
     * @return string
     *
     * @access public
     */
    public function deletePost()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->deletePost();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Prepare and generate the post breadcrumb
     *
     * @return string
     *
     * @access public
     */
    public function postBreadcrumb()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_POST_ACCESS)){
            $post = new aam_View_Post;
            $response = $post->getPostBreadcrumb();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Get Event List
     *
     * @return string
     *
     * @access public
     */
    public function eventList()
    {
        if (aam_View_Collection::hasFeature(self::FEATURE_EVENT_MANAGER)){
            $event = new aam_View_Event;
            $response = $event->retrieveEventList();
        }  else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Save AAM options
     *
     * @return string
     *
     * @access public
     */
    public function save() {
        $this->getSubject()->save(
                apply_filters(
                        'aam_default_option_list', aam_Core_Request::post('aam')
        ));
        return json_encode(array('status' => 'success'));
    }

    /**
     * Roleback changes
     *
     * Restore default settings for current Subject
     *
     * @return string
     *
     * @access public
     */
    public function roleback() {
        //clear all settings
        $this->getSubject()->clearAllOptions();
        //clear cache
        $this->getSubject()->clearCache();

        return json_encode(array('status' => 'success'));
    }

    /**
     * Check if current subject can perform roleback
     *
     * This function checks if there is any saved set of settings and return
     * true if roleback feature can be performed
     *
     * @return string
     *
     * @access public
     */
    public function hasRoleback() {
        return json_encode(
            array(
                'status' => intval(
                        $this->getSubject()->hasFlag(
                                aam_Control_Subject::FLAG_MODIFIED
                        )
                )
        ));
    }

    /**
     * Install license
     *
     * @return string
     *
     * @access public
     */
    public function installLicense()
    {
        if (current_user_can(aam_Core_ConfigPress::getParam(
                        'aam.menu.extensions.capability', 'administrator'
        ))){
            $model = new aam_View_Extension();
            $response = $model->install();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Remove license
     *
     * @return string
     *
     * @access public
     */
    public function removeLicense()
    {
        if (current_user_can(aam_Core_ConfigPress::getParam(
                        'aam.menu.extensions.capability', 'administrator'
        ))){
            $model = new aam_View_Extension();
            $response = $model->remove();
        } else {
            $response = self::DEFAULT_AJAX_RESPONSE;
        }

        return $response;
    }

    /**
     * Save ConfigPress
     *
     * @return string
     *
     * @access public
     */
    public function saveConfigPress()
    {
        if (current_user_can(aam_Core_ConfigPress::getParam(
                        'aam.menu.configpress.capability', 'administrator'
        ))){
            $result = aam_Core_ConfigPress::write(aam_Core_Request::post('config'));
        } else {
            $result = false;
        }

        return json_encode(array(
            'status' => ($result === false ? 'failure' : 'success')
        ));
    }
    
    /**
     * Discard Help Pointer
     * 
     * @return string
     * 
     * @access public
     */
    public function discardHelp(){
        return update_user_meta(get_current_user_id(), 'aam_contextual_menu', 1);
    }

    /**
     * UI Javascript labels
     *
     * @return array
     *
     * @access public
     * @static
     * @todo Move to other file
     */
    public static function uiLabels() {
        return apply_filters('aam_localization_labels', array(
            'Rollback Settings' => __('Rollback Settings', 'aam'),
            'Cancel' => __('Cancelar', 'aam'),
            'Send E-mail' => __('Send E-mail', 'aam'),
            'Add New Role' => __('Agregar Nuevo Rol', 'aam'),
            'Manage' => __('Administrar', 'aam'),
            'Edit' => __('Editar', 'aam'),
            'Delete' => __('Eliminar', 'aam'),
            'Filtered' => __('Filtered', 'aam'),
            'Clear' => __('Clear', 'aam'),
            'Add New Role' => __('Agregar Nuevo Rol', 'aam'),
            'Save Changes' => __('Guardar Cambios', 'aam'),
            'Delete Role with Users Message' => __('El Sistema detecto %d userio(s) con este rol. Todos los usuarios con Rol <b>%s</b> se eliminaran automaticamente!', 'aam'),
            'Delete Role Message' => __('Esta seguro que quiere borrar el rol <b>%s</b>?', 'aam'),
            'Delete Role' => __('Borrar Rol', 'aam'),
            'Add User' => __('Agregar Usuario', 'aam'),
            'Filter Users' => __('Filtrar Usuarios', 'aam'),
            'Refresh List' => __('Refrescar Lista', 'aam'),
            'Block' => __('Bloquear', 'aam'),
            'Delete User Message' => __('Esta seguro que quiere borrar al usuario <b>%s</b>?', 'aam'),
            'Filter Capabilities by Category' => __('Filtrar Capacidades por Categoria', 'aam'),
            'Inherit Capabilities' => __('Heredar Capacidades', 'aam'),
            'Add New Capability' => __('Agregar Nueva Capacidad', 'aam'),
            'Delete Capability Message' => __('Esta seguro que quiere borrar esta capacidad <b>%s</b>?', 'aam'),
            'Delete Capability' => __('Borrar Capacidad', 'aam'),
            'Select Role' => __('Seleccionar Rol', 'aam'),
            'Add Capability' => __('Agregar Capacidad', 'aam'),
            'Add Event' => __('Agregar Evento', 'aam'),
            'Edit Event' => __('Editar Evento', 'aam'),
            'Delete Event' => __('Borrar Evento', 'aam'),
            'Save Event' => __('Guardar Evento', 'aam'),
            'Delete Event' => __('Borrar Evento', 'aam'),
            'Filter Posts by Post Type' => __('Filter Posts by Post Type', 'aam'),
            'Refresh List' => __('Refresh List', 'aam'),
            'Restore Default' => __('Restore Default', 'aam'),
            'Apply' => __('Aplicar', 'aam'),
            'Edit Term' => __('Editar Term', 'aam'),
            'Manager Access' => __('administrar Acceso', 'aam'),
            'Unlock Default Accesss Control' => __('Desbloquear Acceso de Control por Defecto', 'aam'),
            'Close' => __('Cerrar', 'aam'),
            'Edit Role' => __('Editar Rol', 'aam'),
            'Restore Default Capabilities' => __('Restablecer Capacidades por Defecto', 'aam'),
            'Delete Post Message' => __('Esta seguro que quiere borrar <b>%s</b>?', 'aam'),
            'Trash Post Message' => __('Esta seguro que quiere mover <b>%s</b> a papelera?', 'aam'),
            'Delete Post' => __('borrar Post', 'aam'),
            'Delete Permanently' => __('borrar Permanentemente', 'aam'),
            'Trash Post' => __('Trash Post', 'aam'),
            'Restore Default Access' => __('Restore Default Access', 'aam'),
            'Duplicate' => __('Duplicar', 'aam'),
            'Actions Locked' => __('Acciones bloqueadas', 'aam'),
            'AAM Documentation' => __('<h3>AAM Documentation</h3><div class="inner">Find more information about Advanced Access Manager here.</div>', 'aam'),
        ));
    }

}