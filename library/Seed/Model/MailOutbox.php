<?php
/**
 * 邮件发件表模型 (snsys_mail_outboxes)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_MailOutbox extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'mail_outboxes';
    
    /**
     * SMTP服务器
     *
     * @var string
     */
    private $smtp_host;
    
    /**
     * SMTP端口
     *
     * @var string
     */
    private $smtp_port;
    
    /**
     * 是否启用SSL安全连接
     *
     * @var string
     */
    private $smtp_ssl;
    
    /**
     * SMTP用户名
     *
     * @var string
     */
    private $smtp_address;
    
    /**
     * SMTP用户密码
     *
     * @var string
     */
    private $smtp_password;
    
    /**
     * 发件人
     *
     * @var string
     */
    private $smtp_username;
    
    /**
     * 获取最新一封邮件
     *
     * @return	array
     *
     */
    function getLastToSend(){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("is_sended = ?", '0');
            $select->order('send_id DESC');
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 初始化
     *
     * @param	array	$config	配置
     *
     */
    function initConfig($config)
    {
        if(!empty($config) && is_array($config)){
            $this->smtp_host = (isset($config['mail_smtp_host']))? $config['mail_smtp_host']:"";
            $this->smtp_port = (isset($config['mail_smtp_port']))? $config['mail_smtp_port']:"";
            $this->smtp_ssl = (isset($config['mail_smtp_ssl']))? $config['mail_smtp_ssl']:"";
            $this->smtp_address = (isset($config['mail_smtp_address']))? $config['mail_smtp_address']:"";
            $this->smtp_password = (isset($config['mail_smtp_password']))? $config['mail_smtp_password']:"";
            $this->smtp_username = (isset($config['mail_smtp_username']))? $config['mail_smtp_username']:"";
        }
    }
    
    /**
     * 发送邮件
     *
     * @param	type	$send_id	邮件ID
     * @return	boolean
     *
     */
    function sendMail($send_id)
    {
        if(empty($this->smtp_host) || empty($this->smtp_port) || empty($this->smtp_username) || empty($this->smtp_address) || empty($this->smtp_password))return false;
        set_include_path(SEED_LIB_ROOT.'/Plugin/'
        . PATH_SEPARATOR . get_include_path());
        /** PHPmailer */
        require_once SEED_LIB_ROOT.'/Plugin/Phpmailer/class.phpmailer.php';
        
                    
        $outbox = $this->fetchRow(array('send_id'=>$send_id));
        if($outbox['send_id'] > 0){
            $mail = new PHPMailer();
            
            $mail->CharSet = "UTF-8"; // 设置字符集编码 
            $mail->Encoding = "base64";//设置文本编码方式
            $mail->IsSMTP();                    // 启用SMTP
            $mail->Host = $this->smtp_host;            //SMTP服务器
            $mail->SMTPAuth = true;                    //开启SMTP认证
            $mail->Username = $this->smtp_address;            // SMTP用户名
            $mail->Password = $this->smtp_password;                // SMTP密码
            
            $mail->From = $this->smtp_address;            //发件人地址
            $mail->FromName = $this->smtp_username;                //发件人
            $mail->AddAddress($outbox['send_email']);    //添加收件人
            $mail->AddAddress($outbox['send_email']);
            $mail->AddReplyTo($this->smtp_address, $this->smtp_username);    //回复地址
            $mail->WordWrap = 1500;                    //设置每行字符长度
            /** 附件设置
            $mail->AddAttachment("/var/tmp/file.tar.gz");        // 添加附件
            $mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // 添加附件,并指定名称
            */
            $mail->IsHTML(true);                    // 是否HTML格式邮件
            
            $mail->Subject = "=?UTF-8?B?" . base64_encode("".$outbox['send_title']."") . "?=";        //邮件主题
            $mail->Body    = $outbox['send_content'];        //邮件内容
            $mail->AltBody = $outbox['send_content'];    //邮件正文不支持HTML的备用显示
            
            if(!$mail->Send())
            {
               return false;
               //echo "Message could not be sent. <p>";
               //echo "Mailer Error: " . $mail->ErrorInfo;
               //exit;
            }
            
            $this->updateRow(array('is_sended'=>'1','send_time'=>time()),array('send_id'=>$send_id));
            return true;
            //echo "Message has been sent";
        }
        
        return false;
    }
}
