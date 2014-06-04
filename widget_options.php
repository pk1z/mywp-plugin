<?php


class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $settings_page_name = 'uptolike_settings'; //'my-setting-admin';//

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'UpToLike',
            'manage_options',
            $this->settings_page_name, //'my-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    public function statIframe($partnerId, $mail, $cryptKey)
    {
        $params = array(
            'mail' => $mail,
            'partner' => $partnerId
        );
        $paramsStr = 'mail=' . $mail . '&partner=' . $partnerId;
        $signature = md5($paramsStr . $cryptKey);
        $params['signature'] = $signature;
        $finalUrl = 'http://dev3.lembrd.com:7070/api/statistics.html?' . http_build_query($params);

        return $finalUrl;
    }

    public function constructorIframe($mail, $partnerId, $projectId, $cryptKey)
    {

        $params = array('mail' => $mail,
            'partner' => $partnerId,
            'projectId' => $projectId);

        $paramsStr = 'mail=' . $mail . '&partner=' . $partnerId . '&projectId=' . $projectId . $cryptKey;
        $signature = md5($paramsStr);
        $params['signature'] = $signature;
//        $finalUrl = 'http://dev3.lembrd.com:7070/api/constructor.html?' . http_build_query($params);
        $finalUrl = 'http://dev3.lembrd.com:7070/api/constructor.html';
        return $finalUrl;
    }

    /**
     * Options page callback
     */
    public function ilc_admin_tabs($current = 'construct')
    {
        $tabs = array('construct' => 'Конструктор',
            'stat' => 'Статистика',
            'settings' => 'Настройки');

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='#' id=" . $tab . " ref='?page=" . $this->settings_page_name . "&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    public function create_admin_page()
    {
        $this->options = get_option('my_option_name');
        $email = get_settings('admin_email');
        $partnerId = 'cms';
        $projectId = 'cms' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);;
        $options = get_option('my_option_name');
        if (is_array($options) && array_key_exists('id_number', $options)) {
            $cryptKey = $options['id_number'];
        } else $cryptKey = '';
        ?>
        <script type="text/javascript">
            <? include('main.js'); ?>
        </script>
        <style type="text/css">
            h2.placeholder {
                font-size: 1px;
                padding: 1px;
                margin: 0px;
                height: 2px;
            }

            div.wrapper-tab {
                display: none;
            }

            div.wrapper-tab.active {
                display: block;
                width: 100%;
            }

            input#id_number {
                width: 520px;
            }
        </style>
        <div class="wrap">
            <h2 class="placeholder">&nbsp;</h2>
            <?php //screen_icon(); ?>

            <div id="wrapper">
                <form id="settings_form" method="post" action="options.php">

                    <H1> UpToLike виджет</H1>

                    <h2 class="nav-tab-wrapper">

                        <a class="nav-tab nav-tab-active" href="#" id="construct">
                            Конструктор
                        </a>
                        <a class="nav-tab" href="#" id="stat">
                            Статистика
                        </a>

                        <a class="nav-tab" href="#" id="settings">
                            Настройки
                        </a>
                    </h2>

                    <?php //if (isset ($_GET['tab'])) $this->ilc_admin_tabs($_GET['tab']); else $this->ilc_admin_tabs('construct'); ?>

                    <div class="wrapper-tab active" id="con_construct">
                        <iframe id='cons_iframe' style='height: 445px;width: 100%;' src="http://dev3.lembrd.com:7070/api/constructor.html"></iframe>

                        <br>
                        <a onclick="getCode();" href="#">
                            <button type="reset">Сохранить изменения</button>
                        </a>
                    </div>
                    <div class="wrapper-tab" id="con_stat">
                        <? if (('' == $partnerId) OR ('' == $email) OR ('' == $cryptKey)) {

                        ?>
                        <h2>Статистика</h2>
                        <p>Для просмотра статистики необходимо зарегистрироваться или войти.</p>
                        <? } else { ?>
                            <iframe style="width: 100%;height: 380px;" id="stats_iframe" src="
                        <?php echo $this->statIframe($partnerId, $email, $cryptKey); ?>
                        ">
                        </iframe> <?
                        } ?>
                        <button class="reg_btn" type="button">Регистрация</button><br/>
                        <div class="reg_block">
                            <label>Email<input type="text" class="uptolike_email"></label><br/>
                            <button type="button" class="button-primary">Зарегистрироваться</button><br/>
                        </div>
                        <button class="enter_btn" type="button">Вход</button><br/>
                        <div class="enter_block" >
                            <label>Email<input type="text" class="uptolike_email"></label><br/>
                            <label>Ключ<input type="text" class="id_number"></label><br/>
                            <button type="button" class="button-primary">Войти</button><br/>
                        </div>

                    </div>
                    <!-- a41165502@drdrb.net wp OiI7VfCR6OwIY71v3ZsqOIG8QqkHY4qkvb8G4Xl4G6JisRTTigByXhsFgFlXhVdW -->
                    <div class="wrapper-tab" id="con_settings">

                        <?php
                        settings_fields('my_option_group');
                        do_settings_sections($this->settings_page_name);
                        //submit_button();
                        ?>

                        <!--email <? echo get_settings('admin_email'); ?><br>
                        domain <? echo preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']); ?>

                        -->
                        <? //echo $options['widget_code']; ?>
                        <input type="submit" name="submit_btn" value="Cохранить изменения">


                    </div>

                </form>
            </div>
            <!--6aae0985ac9180c746c550669c7a14a8-->
            <!--    <h3>Имя домена - <?php //echo preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']); ?></h3>-->


        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Настройки виджета', // Title
            array($this, 'print_section_info'), // Callback
            $this->settings_page_name//'my-setting-admin' // Page
        );

        add_settings_field(
            'widget_code', // ID
            'код виджета', // Title
            array($this, 'widget_code_callback'), // Callback
            $this->settings_page_name, //'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'data_pid', // ID
            'Ключ(CryptKey)', // Title
            array($this, 'id_number_callback'), // Callback
            $this->settings_page_name, //'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        /*add_settings_field(
            'title', 
            'Title', 
            array( $this, 'title_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        );*/
        //email
        /* add_settings_field(
             'uptolike_email', // ID
             'Ваш email', // Title
             array($this, 'uptolike_email_callback'), // Callback
             $this->settings_page_name, //'my-setting-admin', // Page
             'setting_section_id' // Section
         );
        */
        //uptolike_partner
        /*      add_settings_field(
                  'uptolike_partner', // ID
                  'Ваш partner Id', // Title
                  array( $this, 'uptolike_partner_id_callback' ), // Callback
                  $this->settings_page_name,//'my-setting-admin', // Page
                  'setting_section_id' // Section
              );
              //project Id
              add_settings_field(
                  'uptolike_project', // ID
                  'Ваш project_id', // Title
                  array( $this, 'uptolike_project_callback' ), // Callback
                  $this->settings_page_name,//'my-setting-admin', // Page
                  'setting_section_id' // Section
              );
          */
        add_settings_field(
            'email', //ID
            'email для регистрации',
            array($this, 'uptolike_email_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'on_main', //ID
            'Располагать блок на главной странице',
            array($this, 'uptolike_on_main_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );

        /*        add_settings_field(
                    'before_content', //ID
                    'располагать блок перед текстом статьи',
                    array($this, 'uptolike_before_content_callback'),
                    $this->settings_page_name, //'my-setting-admin',
                    'setting_section_id'
                );
                add_settings_field(
                    'after_content', //ID
                    'располагать блок после текста статьи',
                    array($this, 'uptolike_after_content_callback'),
                    $this->settings_page_name, //'my-setting-admin',
                    'setting_section_id'
                );
          */
        add_settings_field(
            'widget_position', //ID
            'Расположение виджета',
            array($this, 'uptolike_widget_position_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );
        add_settings_field(
            'uptolike_json', //ID
            'настройки конструктора',
            array($this, 'uptolike_json_callback'),
            $this->settings_page_name, //'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();
        if (isset($input['id_number']))
            $new_input['id_number'] = $input['id_number'];

        /*if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );
        */
        if (isset($input['widget_code']))
            $new_input['widget_code'] = $input['widget_code'];

        if (isset($input['uptolike_email']))
            $new_input['uptolike_email'] = $input['uptolike_email'];

        // if( isset( $input['uptolike_partner'] ) )
        //     $new_input['uptolike_partner'] = $input['uptolike_partner'];

        // if( isset( $input['uptolike_project'] ) )
        //     $new_input['uptolike_project'] = $input['uptolike_project'];

        if (isset($input['before_content']))
            $new_input['before_content'] = $input['before_content'];

        if (isset($input['on_main'])) {
                $new_input['on_main'] = true;

        } else  $new_input['on_main'] = false;

        if (isset($input['email']))
            $new_input['email'] = $input['email'];

        if (isset($input['after_content']))
            $new_input['after_content'] = $input['after_content'];

        if (isset($input['widget_position']))
            $new_input['widget_position'] = $input['widget_position'];

        if (isset($input['uptolike_json']))
            $new_input['uptolike_json'] = $input['uptolike_json'];

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        //print 'Enter your settings below:';
    }

    public function widget_code_callback()
    {
        printf(
            '<textarea id="widget_code" name="my_option_name[widget_code]" >%s</textarea>',
            isset($this->options['widget_code']) ? esc_attr($this->options['widget_code']) : ''
        );
    }

    /** 12536473050877
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" class="id_number" name="my_option_name[id_number]" value="%s" />',
            isset($this->options['id_number']) ? esc_attr($this->options['id_number']) : ''
        );
    }

    /*public function uptolike_email_callback()
    {
        printf(
            '<input type="text" id="email" name="my_option_name[email]" value="%s" />',
            isset($this->options['email']) ? esc_attr($this->options['email']) : ''
        );
    }
*/

    /**
     * Get the settings option array and print one of its values
     */
    /*public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }*/
    public function uptolike_email_callback()
    {
        printf(
            '<input type="text" id="uptolike_email" name="my_option_name[uptolike_email]" value="%s" />',
            isset($this->options['uptolike_email']) ? esc_attr($this->options['uptolike_email']) : get_settings('admin_email')
        );
    }

    public function uptolike_json_callback()
    {
        printf(
            '<input type="hidden" id="uptolike_json" name="my_option_name[uptolike_json]" value="%s" />',
            isset($this->options['uptolike_json']) ? esc_attr($this->options['uptolike_json']) : ''
        );
    }

    public function uptolike_partner_id_callback()
    {
        printf(
            '<input type="text" id="uptolike_partner" name="my_option_name[uptolike_partner]" value="%s" />',
            isset($this->options['uptolike_partner']) ? esc_attr($this->options['uptolike_partner']) : ''
        );
    }

    public function uptolike_project_callback()
    {
        printf(
            '<input type="text" id="uptolike_project" name="my_option_name[uptolike_project]" value="%s" />',
            isset($this->options['uptolike_project']) ? esc_attr($this->options['uptolike_project']) : ''
        );
    }

    public function uptolike_on_main_callback()
    {
        echo '<input type="checkbox" id="on_main" name="my_option_name[on_main]"';
        echo ($this->options['on_main'] == true ? 'checked="checked"' : ''); echo '  />';

    }

    /*
    public function uptolike_before_content_callback()
    {

        if (isset($this->options['before_content'])) {
            if (true == $this->options['before_content']) {
                $value = 'checked';
            }
        } else $value = '';
        printf(
            '<input type="checkbox" id="before_content" name="my_option_name[before_content]" %s />', $value
        );
    }

    public function uptolike_after_content_callback()
    {

        if (isset($this->options['after_content'])) {
            if (true == $this->options['after_content']) {
                $value = 'checked';
            }
        } else $value = '';
        printf(
            '<input type="checkbox" id="after_content" name="my_option_name[after_content]" %s />', $value
        );
    }
*/
    public function uptolike_widget_position_callback()
    {
        $top = $bottom = $both = $default = '';

        if (isset($this->options['widget_position'])) {
            if ('top' == $this->options['widget_position']) {
                $top = "selected='selected'";
            } elseif ('bottom' == $this->options['widget_position']) {
                $bottom = "selected='selected'";
            } elseif ('both' == $this->options['widget_position']) {
                $both = "selected='selected'";
            } else {
                $bottom = "selected='selected'";
            }
        } else {
            $my_options = get_option('my_option_name');
            $my_options['widget_position'] = 'bottom'; // cryptkey store
            update_option('my_option_name', $my_options);
        }
        $default = "selected='selected'";
        echo "<select id='widget_position' name='my_option_name[widget_position]'>
                            <option {$top} value='top'>Только сверху</option>
                            <option {$bottom} value='bottom'>Только снизу</option>
                            <option {$both} value='both'>Сверху и снизу</option>
                        </select>";

    }

}


function add_widget($content)
{
    $options = get_option('my_option_name');

    if (is_array($options) && array_key_exists('widget_code', $options)) {
        $widget_code = $options['widget_code'];
        $url = get_permalink();
        $widget_code = str_replace('div data', 'div data-url="' . $url . '" data', $widget_code);
        $widget_code_before = $widget_code_after = '';

        if ((!is_single() && array_key_exists('on_main', $options) && $options['on_main']) or is_single()) {
            if (('top' == $options['widget_position']) or ('both' == $options['widget_position'])) $widget_code_before = $widget_code;
            if (('bottom' == $options['widget_position']) or ('both' == $options['widget_position'])) $widget_code_after = $widget_code;
        }


        return $widget_code_before . $content . $widget_code_after;
    } else return $content;

}

add_filter('the_content', 'add_widget', 6);

/*function my_cryptkey_notice()
{
	//todo проверить случай, первого запуска, если таких ключей  еще нет, что будет
	$options = get_option('my_option_name');
    //MT5iFExzXhcvKXUBCEwyQSKP8Ma5WpV7ZatHR4d0kUsGizHOrxdw1nOPOpRBQcZw
    if ( is_array($options) && array_key_exists('id_number', $options)) { 
		$cryptKey = $options['id_number'];
		if ('' == $cryptKey) {
	        $email = get_settings('admin_email');
	        echo " <div class='updated'>
	                 <p>В настройках укажете ваш CryptKey, он был выслан Вам на почту $email.</p>
	          </div>";
	    }
	}; //else $cryptKey = '';
}
*/
function my_widgetcode_notice()
{
    $options = get_option('my_option_name');
    if (is_array($options) && array_key_exists('widget_code', $options)) {
        $widget_code = $options['widget_code'];
        if ('' == $widget_code) {
            echo " <div class='updated'>
	                 <p>В настройках UpToLike 'Конструктор' выберите тип виджета и нажмите 'Сохранить'</p>
	          </div>";
        }
    }; //else $cryptKey = '';
}

function logger($str)
{
    file_put_contents(WP_PLUGIN_DIR . '/uptolike/log.txt', date(DATE_RFC822) . $str . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function try_reg()
{

    include('api_functions.php');
    $domain = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
    //$email = get_settings('admin_email');
    //if (false == get_option('reg_try')) {
    //пытаемся зарегать, если стоит пустой крипт кей
    $options = get_option('my_option_name');
    $email = $options['uptolike_email'];
    //MT5iFExzXhcvKXUBCEwyQSKP8Ma5WpV7ZatHR4d0kUsGizHOrxdw1nOPOpRBQcZw
    if ('' == $options['id_number']) {
        //if (!is_array($options) or !array_key_exists('id_number', $options) or ('' == $options['id_number'])) {
        //if ( is_array($options) and ( !array_key_exists('id_number', $options) or ('' == $options['id_number']))) {

        //if ('' == get_option('my_option_name')['id_number']) {
        $reg_ans = userReg($email, 'cms', 'cms' . $domain);
        //echo $reg_ans;
        if (is_string($reg_ans)) {
            $my_options = get_option('my_option_name');
            $my_options['id_number'] = $reg_ans; // cryptkey store
            $my_options['choice'] = 'reg';
            update_option('my_option_name', $my_options);

        };
        update_option('reg_try', true);
        //        if (false == $reg_answ)
    }
}

function my_choice_notice()
{
    //todo проверить случай, первого запуска, если таких ключей  еще нет, что будет
    $options = get_option('my_option_name');
    //MT5iFExzXhcvKXUBCEwyQSKP8Ma5WpV7ZatHR4d0kUsGizHOrxdw1nOPOpRBQcZw

    //if (is_bool($options) or (!array_key_exists('choice', $options)) OR (!('ignore' == $options['choice']) AND ('' == $options['id_number']))) {
    if (is_bool($options) or (('' == $options['id_number']) and ((!array_key_exists('choice', $options)) OR ('ignore' !== $options['choice'])))) {
        //$cryptKey = $options['id_number'];
        //if ('' == $cryptKey) {
        //    $email = get_settings('admin_email');
        echo "<div class='updated'>
<div><span class='logo-img' style='background: url(//uptolike.ru/img/logo.png) no-repeat scroll 0 0 transparent;
display: inline-block;
float: left;
width: 40px;
height: 40px;'></span>
</div>
Кнопки успешно установлены! <br>Для просмотра статистики было бы неплохо:
        <a  href='options-general.php?page=uptolike_settings#enter'>Войти</a>
        | <a href='options-general.php?page=uptolike_settings#reg'>Зарегистрироваться</a>
        | <a href='options-general.php?page=uptolike_settings&choice=ignore'>Скрыть</a> </div>";

        //}
    }; //else $cryptKey = '';
}

function set_default_code()
{
    $options = get_option('my_option_name');
    if (is_bool($options)) {
        $options = array();
    }
    $data_url = 'cms' . $_SERVER['HTTP_HOST'];
    $data_pid = 'cms' . str_replace('.', '', $_SERVER['HTTP_HOST']);
    //$data_pid = 0;

    $code = file_get_contents(WP_PLUGIN_DIR . '/uptolike/test.html');
    $code = str_replace('data-pid', 'data-pid="' . $data_pid . '"', $code);
    $code = str_replace('data-url', 'data-url="' . $data_url . '"', $code);
    $options['widget_code'] = $code;
    $options['on_main'] = true;
    $options['widget_position'] = 'bottom';


    update_option('my_option_name', $options);
}

function choice_helper($choice)
{
    $options = get_option('my_option_name');
    $options['choice'] = $choice;
    //$choice='ignore';
    if ('ignore' == $choice) {
        set_default_code();
    }
    update_option('my_option_name', $options);
}

add_action('admin_notices', 'my_choice_notice');
//add_action('admin_notices', 'my_cryptkey_notice');
add_action('admin_notices', 'my_widgetcode_notice');

//set_default_code();

$options = get_option('my_option_name');


if (is_admin()) {
    $options = get_option('my_option_name');


    if (array_key_exists('regme', $_REQUEST)) {
        try_reg();
//        choice_helper($_REQUEST['choice']);
    }
    if (array_key_exists('choice', $_REQUEST)) {
        choice_helper($_REQUEST['choice']);
    }

    $my_settings_page = new MySettingsPage();
    if (is_bool($options) OR (!array_key_exists('widget_code', $options)) OR ('' == $options['widget_code'])) {
        set_default_code();
    }


}