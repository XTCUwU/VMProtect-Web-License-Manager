<?php
/* Translated by VDOWN ( http://vdown.cn/vmprotect ) */

define("HELP_IMPORT_LICENSE", "您可以在此粘贴在某些地方生成的授权（例如手动生成的）, 完成后该授权会被选定并添加到数据库中.");
define("HELP_NEW_PRODUCT", "可通过填写以下表格来创建一个新的产品. 请仔细选择密钥的长度, 较大的值意味着更安全, 但处理速度较慢, 及较长的序列号. 建议如不确定, 请保留默认值.");
define("HELP_IMPORT_PRODUCT", "选定一个本地的VMProtect项目文件, 并将其上传到数据库, 其所有序列号和其他相关授权属性也会保存在数据库中. 您可以使用\"导出\" 命令并下载项目文件. 此功能对用于同步目的非常有用.");
define("HELP_NEW_REGISTRATOR", "请将您的电子商务供应商名称填写在此, 并且也可填写其IP地址范围, 这样授权管理系统将只接受来自这些IP地址的注册生成器请求.");
define("HELP_COPY_URL", "请在下拉列表中选择您的电子商务提供商, 将授权 KEY 生成器URL连接填写在电子商务提供商控制模板中. 如果您的供应商没有在这里列出, 请使用同样的规则和模板创建您自己的网址, 并发给我们. 我们可以在下次更新版本增加.");

define("VERSION_TXT", "版本");

/* Login page */
define("LOGIN_HEADER_TXT", "登录");
define("LOGIN_BTN_TXT", "登录");
define("LOGIN_ERROR_TXT", "错误的用户名或密码.");
define("USERNAME_TXT", "用户名");
define("PASSWORD_TXT", "密码");
define("FORGOT_TXT", "忘记密码?");

/* Retrive password page */
define("RP_RETPASS_TXT", "恢复密码");
define("RP_EMAIL_TXT", "Email");
define("RP_SEND_TXT", "发送");
define("RP_USERNOTFOUND_TXT", "未找到该用户.");
define("RP_SENT_TXT", "新密码已经发到您的 email 地址.");
define("RP_ERROR_TXT", "发送邮件出错.");

define("HELLO_TXT", "您好");
define("HELP_TXT", "帮助");
define("LOGOUT_TXT", "退出");

define("M_LICS_TXT", "授权管理");
define("M_NEWLIC_TXT", "添加新授权");
define("H_EDITLIC_TXT", "编辑授权");
define("M_IMPORTLIC_TXT", "导入授权");
define("M_ACTS_TXT", "激活码");
define("M_NEWACT_TXT", "添加新代码");
define("H_NEWACT_TXT", "添加激活码");
define("H_EDITACT_TXT", "编辑激活码");
define("M_IMPORTACT_TXT", "导入代码");
define("H_IMPORTACT_TXT", "导入激活码");
define("M_PRODS_TXT", "产品管理");
define("M_NEWPROD_TXT", "添加新产品");
define("H_EDITPROD_TXT", "编辑产品信息");
define("M_NEWMODE_TXT", "添加新模式");
define("H_EDITMODE_TXT", "编辑模式");
define("M_IMPORTPROD_TXT", "导入产品信息");
define("M_REGS_TXT", "注册管理");
define("M_NEWREG_TXT", "添加新注册者");
define("H_EDITREG_TXT", "编辑注册者");
define("M_USERS_TXT", "用户管理");
define("M_NEWUSER_TXT", "添加新用户");
define("H_EDITUSER_TXT", "编辑用户");

define("ADD_TXT", "添加新的");
define("ITEMS_TXT", "条目");
define("ALL_TXT", "全部");
define("BLOCKED_TXT", "冻结的");
define("INACTIVE_TXT", "非活动的");
define("SAVE_TXT", "保存");
define("IMPORT_TXT", "导入");
define("EDIT_TXT", "编辑");
define("DELETE_TXT", "删除");
define("CANCEL_TXT", "取消");
define("BLOCK_TXT", "冻结");
define("UNBLOCK_TXT", "解冻");
define("COPYCPB_TXT", "复制到剪贴板");

/* Licenses */
define("LIC_DATE_TXT", "创建日期");
define("LIC_PROD_TXT", "产品");
define("LIC_NAME_TXT", "客户名字");
define("LIC_EMAIL_TXT", "客户Email");
define("LIC_ORDERREF_TXT", "订单参考号");
define("LIC_COMMENTS_TXT", "备注");
define("LIC_HWID_TXT", "硬件 ID");
define("LIC_EXPDATE_TXT", "失效日期");
define("LIC_LIMIT_TXT", "时间限制");
define("LIC_MAXBDATE_TXT", "最后创建日期");
define("LIC_DATA_TXT", "数据");
define("LIC_BLOCKED_TXT", "冻结的");
define("LIC_COPYSN_TXT", "复制序列号");
define("LIC_SN_TXT", "授权序列号");
define("LIC_DELETE_TXT", "删除授权");
define("LIC_DELMSG_TXT", "您确定要删除该授权?");
define("LIC_ENOTP_TXT", "未找到用于该序列号的产品信息.");

/* Activations */
define("ACT_CODE_TXT", "代码");
define("ACT_COUNT_TXT", "激活数量");
define("ACT_USED_TXT", "已用");
define("ACT_LICS_TXT", "显示授权");
define("ACT_BLOCKED_TXT", "冻结的");
define("ACT_DELETE_TXT", "删除激活码");
define("ACT_DELMSG_TXT", "您确定要删除该激活码?");
define("ACT_CODES_TXT", "激活码");
define("ACT_ECODES_TXT", "代码错误.");
define("ACT_NONE_TXT", "无");
define("ACT_FROMURL_TXT", "从URL连接");
define("ACT_DELAY_TXT", "天的激活日期");
define("ACT_EXPDELAY_TXT", "自購買之日起天");
define("ACT_VALUE_TXT", "值");
define("ACT_EXPDATE_TXT", "失效日期");

/* Products */
define("PR_NAME_TXT", "产品名称");
define("PR_MODE_TXT", "添加模式");
define("PR_LICS_TXT", "显示授权");
define("PR_EXPORT_TXT", "导出");
define("PR_KEYGEN_TXT", "注册生成器URL连接");
define("PR_REG_TXT", "选择模板");
define("PR_ACTIVE_TXT", "活跃的");
define("PR_ALG_TXT", "算法");
define("PR_BITS_TXT", "Bits");
define("PR_KGMODE_TXT", "注册生成器模式");
define("PR_ACTPAT_TXT", "激活码样式");
define("PR_ACTEX_TXT", "额外激活");
define("PR_DELETE_TXT", "删除产品");
define("PR_DELMSG_TXT", "您确定要删除该产品信息?");
define("PR_FILE_TXT", "产品文档");
define("PR_KGSN_TXT", "序列号");
define("PR_KGAC_TXT", "激活码");
define("PR_ENAME_TXT", "已存在使用该名字的产品.");
define("PR_EFILESIZE_TXT", "文件太大. 最大文件大小 %d.");
define("PR_EXMLFILE_TXT", "解析XML文件出错.");
define("PR_EXMLDATA_TXT", "XML数据错误.");
define("PR_IMPORTED_TXT", "已导入产品 \'%s\' 个及 %d 个授权.");

/* Registrators */
define("R_NAME_TXT", "注册者姓名");
define("R_RANGES_TXT", "IP 范围");
define("R_START_TXT", "开始 IP");
define("R_END_TXT", "结束 IP");
define("R_ADD_TXT", "添加");
define("R_ACTIVE_TXT", "活跃的");
define("R_DELETE_TXT", "删除注册者");
define("R_DELMSG_TXT", "您确定要删除该注册者?");
define("R_EIP_TXT", "非有效IP地址.");
define("R_EOCTET_TXT", "每个数值必须是介于0和255之间.");
define("R_EENDIP_TXT", "结束IP应该更大.");
define("R_EIPS_TXT", "需要添加至少一个IP.");
define("R_AUTHMODE_TXT", "授权模式");
define("R_AUTHMODE_IP_TXT", "以IP方式访问");
define("R_AUTHMODE_LOGIN_TXT", "以登录用户名和密码方式访问");
define("R_LOGIN_TXT", "登录");
define("R_PASSWORD_TXT", "密码");
define("R_VALUE_TXT", "值");

/* Users */
define("U_ADMINS_TXT", "系统管理员");
define("U_MANS_TXT", "产品经理");
define("U_ADMIN_TXT", "系统管理员");
define("U_MAN_TXT", "产品经理");
define("U_ROLE_TXT", "角色");
define("U_UN_TXT", "用户名");
define("U_PASS_TXT", "密码");
define("U_PASS2_TXT", "确认密码");
define("U_EMAIL_TXT", "Email");
define("U_DELETE_TXT", "删除用户");
define("U_DELMSG_TXT", "您确定要删除该用户?");
define("U_PROFILE_TXT", "个人资料");
define("U_ELOGIN_TXT", "该用户登录名已经使用中.");
define("U_EEMAIL_TXT", "该Email已经使用中.");
define("U_EADMIN_TXT", "不能删除第一个用户.");
define("U_ESELF_TXT", "不能自己删除自己的用户名.");

/* Validation script */
define("V_DIG_TXT", "仅允许使用数字.");
define("V_REQ_TXT", "此字段是必需.");
define("V_EMAIL_TXT", "非有效Email地址.");
define("V_DATE_TXT", "非有效日期格式. 格式是 YYYY(年)-MM(月)-DD(日).");
define("V_EQ_TXT", "应该等同于");
define("V_ERROR_TXT", "错误");

/* Installation */
define("I_MODULES_TXT", "正在检查PHP模块");
define("I_CONTINUE_TXT", "继续");
define("I_OK_TXT", "OK");
define("I_FAILED_TXT", "失败");
define("I_EMODULE_TXT", "请先安装必需的PHP模块再继续.");
define("I_EGLOBALS_TXT", "请禁用PHP中 'register_globals' 选项.");
define("I_DATABASE_TXT", "正在连接数据库");
define("I_DBSERVER_TXT", "数据库服务器");
define("I_DBNAME_TXT", "数据库名称");
define("I_DBUSER_TXT", "数据库用户名");
define("I_DBPASS_TXT", "数据库密码");
define("I_DBPREFIX_TXT", "表前缀");
define("I_INSTALL_TXT", "安装数据表");
define("I_UPDATE_TXT", "更新数据表");
define("I_CONNECT_TXT", "连接");
define("I_DBEXISTS_TXT", "数据表已经存在, 您可以选择以更新方式保存原有数据, 或完成一次纯净的安装.");
define("I_ADMIN_TXT", "创建系统管理员");
define("I_CREATE_TXT", "创建");
define("I_SAVING_TXT", "正在保存配置");
define("I_ERROR_TXT", "无法写入文件 'include/config.inc.php'");
define("I_SUCCESS_TXT", "配置文件已经成功保存.<br/>现在可以禁用文件 'include/config.inc.php' 的可改写属性.<br />请删除服务器上的 'install.php' 文件已完成安装步骤, 并点击 <a href=\"index.php\">这里</a> 开始工作.");

/* Offline activation */
define("OFF_STRING_TXT", "输入您的激活 / 停用字符串:");
define("OFF_SUBMIT_TXT", "提交");
define("OFF_SN_TXT", "您的序列号:");
define("OFF_COPIED_TXT", "已复制");
define("OFF_ESTRING_TXT", "激活字串错误");
define("OFF_ECODE_TXT", "您的激活码无效");
define("OFF_ELIMIT_TXT", "已超过激活限制");
define("OFF_EBLOCKED_TXT", "您的激活码被冻结");
define("OFF_EEXPIRED_TXT", "您的激活码已经过期");
define("OFF_ESN_TXT", "您的序列号无效");
define("OFF_DEACT_TXT", "您的序列号已停用");

?>