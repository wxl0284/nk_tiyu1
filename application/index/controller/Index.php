<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
//use think\Validate;

class Index extends Controller
{
	
	/*
	 index(): 如果是移动设备访问显示移动端页面,在此方法里执行统一身份认证，并且在前端显示的页面里用WebSocket发送数据给训练终端
	 否则就执行登录进后台
	*/
	
    public function index()
    {
    	if ( $this->request->isMobile() )
    	{
    		//获取扫码后得到的训练终端的ip地址
			$d = input();

			if ( isset($d['address']) )
			{
				$ip = $d['address'];
			}else{
				$ip = '11';//随便给个值，防止页面报错
			}//获取ip地址 结束

    		//$ip = '192.168.8.2';//扫码获得的 训练终端的ip
    		$stu_grade = '2019';//年级
    		$stu_class = '天文1班';//班级
    		$stu_num = '888';//学生号
    		$stu_name = 'li'; //学生姓名		
    		$type = 1; //1表示学生 2 表示教师
			
			//查询最新一条通知信息
			$notice = Db::table('tp_notice')->order('id', 'desc')->field('content')->find();
			$content = '';//通知内容

			if ($notice)
			{
				$content = $notice['content'];
			}

			if ($type == 1)
			{//学生登录
				//查询tp_video表里的每门课程包括的所有动作
				$r = Db::table('tp_video')->group('video_lession')->column('video_lession');//所有的课程

				if ($r)
				{//有课程信息
					//然后查video_lession字段==$r[0]的所有视频  例如网球
					$video = Db::table('tp_video')->where('video_lession', $r[0])->field('video_name,video_pic,video')->order('v_id', 'asc')->select();
					
					if ($video)
					{//有视频信息
						//return view()->assign(['list' => $r, 'videos' => $video, 'lession' => 1]);
						return view('stu_page')->assign([
							'ip' => $ip,
							'stu_grade' => $stu_grade,
							'stu_class' => $stu_class,
							'stu_num' => $stu_num,
							'stu_name' => $stu_name,
							'type' => $type,
							'list' => $r,
							'videos' => $video,
							'lession' => 1,
							'notice' => $content
							]);
					}else
					{//无视频信息
						//return view()->assign(['list' => $r, 'lession' => 1]);
						return view('stu_page')->assign([
							'ip' => $ip,
							'stu_grade' => $stu_grade,
							'stu_class' => $stu_class,
							'stu_num' => $stu_num,
							'stu_name' => $stu_name,
							'type' => $type,
							'list' => $r,
							'lession' => 1,
							'notice' => $content
							]);
					}					
					
				}else{//无课程信息
					
					return view('stu_page')->assign([
						'ip' => $ip,
						'stu_grade' => $stu_grade,
						'stu_class' => $stu_class,
						'stu_num' => $stu_num,
						'stu_name' => $stu_name,
						'type' => $type,
						'notice' => $content
						]);
				}

			}else if($type == 2)
			{//是老师登录
				return view()->assign([
					'ip' => $ip,
					'stu_grade' => $stu_grade,
					'stu_class' => $stu_class,
					'stu_num' => $stu_num,
					'stu_name' => $stu_name,
					'type' => $type,
					'notice' => $content
					]);
			}
    		
    	}else{
    		$this->redirect('admin/pub/login');//登录进后台
    	}
        
	}//index() 结束
	
	/*
	index_0(): 此方法加入了统一身份认证，要替代index()方法
	*/

	public function index_0 ()
	{
		$is_mobile = $this->request->isMobile();
		$ticket = isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"]); //$_REQUEST["ticket"] 统一身份认证的请求参数
		
		//获取扫码后得到的训练终端的ip地址
		$d = input();

		if ( isset($d['address']) )
		{
			$ip = $d['address'];
		}else{
			$ip = '11';//随便给个值，防止页面报错
		}//获取ip地址 结束

		$loginServer     = "https://sso.nankai.edu.cn/sso/login"; //南开统一身份认证登录地址
		$validateServer  = "https://sso.nankai.edu.cn/sso/serviceValidate"; //南开cas服务器验证地址
		$address         = "http://体育项目的域名"; //即南开统一身份认证的回调地址

		if ( $is_mobile || $ticket || ($is_mobile && session('user')) )
    	{//手机访问，或者南开统一身份认证回调，
			if ( $ticket || !session('user') )
			{//进行统一身份认证
				try {
					// url里带上ticket去cas服务验证地址
					$validate_url = $validateServer. "?ticket=" . $_REQUEST["ticket"] . "&service=" . $address;
					header("Content-Type:text/html;charset=utf-8");
					
					//服务端为https，需加以下配置
					$arrContextOptions = [
						"ssl" => [
							"verify_peer" => false,
							"verify_peer_name" => false]
					];
	
					$abc = urldecode(file_get_contents( $validate_url, false, stream_context_create($arrContextOptions) ) );//https的请求
					
					//$abc = urldecode( file_get_contents($validate_url) ); //http请求时
						
					$dom = new \DOMDocument(); //创建一个dom文档
	
					$dom->preserveWhiteSpace = false; //忽略xml命名空间
					$dom->encoding = "utf-8";
					$dom->loadXML($abc);
					/*
					获取用户的唯一标识信息
					由UIA的配置不同可分为两种：
					(1)学生：学号；教工：身份证号
					(2)学生：学号；教工：教工号
					*/
	
					$extra_attributes = []; //存储师生信息数组
					
					// CAS服务器只允许utf-8格式的数据
					$success = $dom->getElementsByTagName("authenticationSuccess");
	
					if( $success->length != 0 )
					{
						$item      = $success->item(0);
						$item_user = $item->getElementsByTagName("user");
						
						if ( $item_user->length == 0 )
						{
							//header("Location: " . $loginServer . "?service=" . $address);
							$this->redirect( $loginServer . "?service=" . $address );
						}
						else 
						{
							$attr_nodes = $item->getElementsByTagName("attributes");
	
							if ( $attr_nodes->length != 0 )
							{
								if ( $attr_nodes->item(0)->hasChildNodes() )
								{
									foreach ( $attr_nodes->item(0)->childNodes as $attr_child )
									{
										_addAttributeToArray( $extra_attributes, $attr_child->localName, $attr_child->nodeValue );
									}
								}
							}
						}
					}//$success->length != 0 结束
					else
					{
						//header("Location:" . $loginServer . "?service=" . $address); //无法访问
						$this->redirect( $loginServer . "?service=" . $address );
					}
			
					//获取师生的信息	
					$res['user_name']       = isExistInArray($extra_attributes,"comsys_name"); // 用户姓名
					$res['teaching_number'] = isExistInArray($extra_attributes,"comsys_teaching_number");// 教工号               
					$res['student_number']  = isExistInArray($extra_attributes,"comsys_student_number");// 学生号
					$res['type'] 			= isExistInArray($extra_attributes,"comsys_usertype");// 获取用户类型   1-学生  2-教工                
					$res['className'] 		= isExistInArray($extra_attributes,"comsys_classname");// 学生班级名称					
					$res['gradName']        = isExistInArray($extra_attributes,"comsys_gradename");// 学生年级名称
					$res['faculety']        = isExistInArray($extra_attributes,"comsys_faculetyname");//院系名称
					//$res['major']         = isExistInArray($extra_attributes,"comsys_disciplinename"); // 学生专业名称
					
					//--------------------将用户信息写入session, 查询页面所需数据并显示-----------------
					session('user', $res['user_name']);
					
					if ($res['teaching_number'])
					{
						session('stu_num', $res['teaching_number']);
					}else if ($res['student_number'] )
					{
						session('stu_num', $res['student_number']);
					}

					session('type', $res['type']);

					if ($res['className'])
					{
						session('stu_class', $res['className']);
					}else
					{
						session('stu_class', '000'); //给个值，防止前端页面报错
					}

					if ($res['gradName'])
					{
						session('stu_grade', $res['gradName']);
					}else
					{
						session('stu_grade', '000'); //给个值，防止前端页面报错
					}

					session('faculety', $res['faculety']);
					//开始查询页面所需数据并显示

					//查询最新一条通知信息
					$notice = Db::table('tp_notice')->order('id', 'desc')->field('content')->find();
					$content = '';//通知内容

					if ($notice)
					{
						$content = $notice['content'];
					}

					if ($res['type'] == 1)
					{//是学生登录
						//查询tp_video表里的每门课程包括的所有动作
						$r = Db::table('tp_video')->group('video_lession')->column('video_lession');//所有的课程

						if ($r)
						{//有课程信息
							//然后查video_lession字段==$r[0]的所有视频  例如网球
							$video = Db::table('tp_video')->where('video_lession', $r[0])->field('video_name,video_pic,video')->order('v_id', 'asc')->select();
							
							if (!$video)
							{//有视频信息
								$video = null;
							}
							
							return view('stu_page')->assign([
								'ip'        => $ip,
								'stu_grade' => session('stu_grade'),
								'stu_class' => session('stu_class'),
								'stu_num'   => session('stu_num'),
								'stu_name'  => session('user'),
								'type'      => $res['type'],
								'list'      => $r,
								'videos'    => $video,
								'lession'   => 1,
								'notice'    => $content
								]);
							
						}else{//无课程信息
							
							return view('stu_page')->assign([
								'ip'        => $ip,
								'stu_grade' => session('stu_grade'),
								'stu_class' => session('stu_class'),
								'stu_num'   => session('stu_num'),
								'stu_name'  => session('user'),
								'type'      => $res['type'],
								'notice'    => $content
								]);
						}

					}else if($res['type'] == 2)
					{//是老师登录
						return view()->assign([
							'ip'        => $ip,
							'stu_grade' => session('stu_grade'),
							'stu_class' => session('stu_class'),
							'stu_num'   => session('stu_num'),
							'stu_name'  => session('user'),
							'type'      => $res['type'],
							'notice'    => $content
							]);
					}
					//--------------------将用户信息写入session, 查询页面所需数据并显示 结束-----------------
				}//try 结束
				catch (Exception $e)
				{//认证失败，再次显示统一身份认证登录页面
					$this->redirect( $loginServer . "?service=" . $address );
				}
				//统一身份认证 结束
			}else if( $is_mobile && !$ticket && session('user') )
			{//手机访问，已登录
				//查询最新一条通知信息
				$notice = Db::table('tp_notice')->order('id', 'desc')->field('content')->find();
				$content = '';//通知内容

				if ($notice)
				{
					$content = $notice['content'];
				}

				if (session('type') == 1)
				{//是学生登录
					//查询tp_video表里的每门课程包括的所有动作
					$r = Db::table('tp_video')->group('video_lession')->column('video_lession');//所有的课程

					if ($r)
					{//有课程信息
						//然后查video_lession字段==$r[0]的所有视频  例如网球
						$video = Db::table('tp_video')->where('video_lession', $r[0])->field('video_name,video_pic,video')->order('v_id', 'asc')->select();
						
						if (!$video)
						{//有视频信息
							$video = null;
						}
						
						return view('stu_page')->assign([
							'ip'        => $ip,
							'stu_grade' => session('stu_grade'),
							'stu_class' => session('stu_class'),
							'stu_num'   => session('stu_num'),
							'stu_name'  => session('user'),
							'type'      => session('type'),
							'list'      => $r,
							'videos'    => $video,
							'lession'   => 1,
							'notice'    => $content
							]);
						
					}else{//无课程信息
						
						return view('stu_page')->assign([
							'ip'        => $ip,
							'stu_grade' => session('stu_grade'),
							'stu_class' => session('stu_class'),
							'stu_num'   => session('stu_num'),
							'stu_name'  => session('user'),
							'type'      => session('type'),
							'notice'    => $content
							]);
					}

				}else if(session('type') == 2)
				{//是老师登录
					return view()->assign([
						'ip'        => $ip,
						'stu_grade' => session('stu_grade'),
						'stu_class' => session('stu_class'),
						'stu_num'   => session('stu_num'),
						'stu_name'  => session('user'),
						'type'      => session('type'),
						'notice'    => $content
						]);
				}
			}//手机访问，已登录 结束
			
		}else if ( $is_mobile && !$ticket && !session('user') )
		{
			$this->redirect( $loginServer . "?service=" . $address );//显示统一身份认证页面
		}
		else if ( !$is_mobile && !$ticket )
		{//pc端访问的话直接进行后台登录
    		$this->redirect('admin/pub/login');//登录进后台
    	}
	}//index_0() 结束

	//check_user() ilab-x 统一身份认证的代码 
	public function check_user ()
    {
        $d = input('');
		
		if ( isset($d['admin_log']) )
		{
			$address = "https://ilab-x.nankai.edu.cn/api/check_user?admin_log=01";//带个参数，让南开再请求时区别去做实验的身份验证操作
		}else{
			$address = "https://ilab-x.nankai.edu.cn/api/check_user";//不带参数，表示是去做实验的身份认证操作
		}
		
        $experiment_id = cookie('experiment_id'); //判断并获取要做实验的experiment_id      
		
        $loginServer    = "https://sso.nankai.edu.cn/sso/login"; //南开统一身份认证登录地址
    
        $validateServer = "https://sso.nankai.edu.cn/sso/serviceValidate"; //南开cas服务器验证地址
		
        if ( isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"]) )
        {
            try {
                // url里带上ticket去cas服务验证地址
                $validate_url = $validateServer."?ticket=".$_REQUEST["ticket"]."&service=".$address;
                header("Content-Type:text/html;charset=utf-8");
                //服务端为https，需加以下配置
                $arrContextOptions = [
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false]
                ];

                $abc = urldecode(file_get_contents( $validate_url, false, stream_context_create($arrContextOptions) ) );
                
                //$abc = urldecode( file_get_contents($validate_url) ); //http使用这种方式
            		
                $dom = new \DOMDocument(); // 创建一个dom文档

                $dom->preserveWhiteSpace = false; //忽略xml命名空间
                $dom->encoding = "utf-8";
                $dom->loadXML($abc);
                /*
                获取用户的唯一标识信息
                由UIA的配置不同可分为两种：
                (1)学生：学号；教工：身份证号
                (2)学生：学号；教工：教工号
                */

                $extra_attributes = [];
                
                // CAS服务器只允许utf-8格式的数据
                $success = $dom->getElementsByTagName("authenticationSuccess");

                if( $success->length != 0 )
                {
                    $item      = $success->item(0);
                    $item_user = $item->getElementsByTagName("user");
                    
                    if ( $item_user->length == 0 )
                    {
                        header("Location: " . $loginServer . "?service=" . $address);
                    }
                    else 
                    {
                        $attr_nodes = $item->getElementsByTagName("attributes");

                        if ( $attr_nodes->length != 0 )
                        {
                            if ( $attr_nodes->item(0)->hasChildNodes() )
                            {
                                foreach ( $attr_nodes->item(0)->childNodes as $attr_child )
                                {
                                    _addAttributeToArray( $extra_attributes, $attr_child->localName, $attr_child->nodeValue );
                                }
                            }
                        }
                    }
                }
                else
                {
                    //header("Location:" . $loginServer . "?service=" . $address); //无法访问
					$this->redirect( $loginServer . "?service=" . $address );
                }
        
                // 获取登录用户的信息
                $res['password'] = '123456'; // password字段为必填
                $res['name'] = isExistInArray($extra_attributes,"comsys_name"); // name字段为必填 用户姓名
                
                if ( !$res['name'] )
                {
                    $res['name'] = ' ';//name 字段必填
                }

                $res['user_name'] = isExistInArray($extra_attributes,"comsys_name"); // 用户姓名
                $res['phone'] = isExistInArray($extra_attributes,"comsys_phone"); // 电话号码
                
                if ( !$res['phone'] )
                {
                    $res['phone'] = '13612326265';
                }

                $res['sex'] = isExistInArray($extra_attributes,"comsys_genders"); // 性别               
                $res['email'] = isExistInArray($extra_attributes,"comsys_email");// 邮件
                $res['teaching_number'] = isExistInArray($extra_attributes,"comsys_teaching_number");// 教工号                
                $res['student_number'] = isExistInArray($extra_attributes,"comsys_student_number");// 学生号                
                $res['type'] = isExistInArray($extra_attributes,"comsys_usertype");// 获取用户类型   1-学生  2-教工                
                $res['major'] = isExistInArray($extra_attributes,"comsys_disciplinename"); // 学生专业名称
                
                if ( isset($d['admin_log']) ) //南开老师用统一身份认证登录进后台
                {
                    $info = Db::table('tp_admin_user')->where('account', $res['teaching_number'])->find();

                    if (!$info)//第一次登录 把用户信息写入tp_admin_user表
                    {
                        //把教师数据写入此表中
                        $temp = [
                            'account'    => $res['teaching_number'],
                            'realname'   => $res['user_name'],
                            'type'       => 2,//教师角色
                            'status'     => 1,//账号 启用
                            'password'   => password_hash_tp('123456'),//初始密码123456
                        ];

                        $r_id = Db::table('tp_admin_user')->insertGetId( $temp );

                        if ($r_id)
                        {
                            // 生成session信息
                            Session::set(Config::get('rbac.user_auth_key'), $r_id);
                            Session::set('user_name', $res['teaching_number']);
                            Session::set('real_name', $res['user_name']);
                            Session::set('last_login_ip', '');
                            Session::set('last_login_time', 0);
                            Session::set('type', 2);
                          
                            // 保存登录信息
                            $update['last_login_time'] = time();
                            $update['login_count'] = ['exp', 'login_count+1'];
                            $update['last_login_ip'] = $this->request->ip(0,1);
                            Db::name("AdminUser")->where('id', $r_id)->update($update);

                            // 记录登录日志
                            $log['uid'] = $r_id;
                            $log['login_ip'] = $update['last_login_ip'];
                            $log['login_location'] = implode(" ", \Ip::find($log['login_ip']));
                            $log['login_browser'] = \Agent::getBroswer();
                            $log['login_os'] = \Agent::getOs();
                            Db::name("LoginLog")->insert($log);
							
							//进行做实验前的数据准备
							if ( $res['teaching_number'] )
							{
								$res['nankai_user_id'] = $res['teaching_number'];
								unset($res['teaching_number'], $res['student_number']);
							}else if ( $res['student_number'] )
							{
								$res['nankai_user_id'] = $res['student_number'];
								unset($res['teaching_number'], $res['student_number']);
							}

							if ( $res['type'] == 1 )//学生
							{
								$res['user_type'] = 2;
								unset($res['type']);
							}else if( $res['type'] == 2 )//老师
							{
								$res['user_type'] = 3;
								unset($res['type']);
							}

							$data = Db::table('tp_user')->where('nankai_user_id', $res['nankai_user_id'])->find();

							if ( !$data )//还没有此用户
							{
								$tp_user_id = Db::table('tp_user')->insertGetId($res);
								
								if ($tp_user_id)
								{
									$info = Db::table('tp_user')->where('id', $tp_user_id)->find();
									session::set('home_info', $info);//原有登录功能有此session 记录日志用的
									session::set('home_user_id', $tp_user_id);
									cookie('user_type', $res['user_type']);
								}
							}else//已有此用户
							{
								session::set('home_info', $data);//原有登录功能有此session 记录日志用的
								session::set('home_user_id', $data['id']);
								cookie('user_type', $res['user_type']);
							}
							//进行做实验前的数据准备 结束

                            // 缓存访问权限
                            \Rbac::saveAccessList();
                            $this->redirect('admin/index/index');

                        }else
                        {
                            $this->error('网络异常，请重新登录');
                        }                      

                    }else//已是再次登录
                    {
                        // 生成session信息
                        Session::set(Config::get('rbac.user_auth_key'), $info['id']);
                        Session::set('user_name', $info['account']);
                        Session::set('real_name', $info['realname']);
                        Session::set('last_login_ip', $info['last_login_ip']);
                        Session::set('last_login_time', $info['last_login_time']);
                        Session::set('type', $info['type']);
                      
                        // 保存登录信息
                        $update['last_login_time'] = time();
                        $update['login_count'] = ['exp', 'login_count+1'];
                        $update['last_login_ip'] = $this->request->ip(0,1);
                        Db::name("AdminUser")->where('id', $info['id'])->update($update);

                        // 记录登录日志
                        $log['uid'] = $info['id'];
                        $log['login_ip'] = $update['last_login_ip'];
                        $log['login_location'] = implode(" ", \Ip::find($log['login_ip']));
                        $log['login_browser'] = \Agent::getBroswer();
                        $log['login_os'] = \Agent::getOs();
                        Db::name("LoginLog")->insert($log);
						
						//进行做实验前的数据准备
						if ( $res['teaching_number'] )
							{
								$res['nankai_user_id'] = $res['teaching_number'];
								unset($res['teaching_number'], $res['student_number']);
							}else if ( $res['student_number'] )
							{
								$res['nankai_user_id'] = $res['student_number'];
								unset($res['teaching_number'], $res['student_number']);
							}

							if ( $res['type'] == 1 )//学生
							{
								$res['user_type'] = 2;
								unset($res['type']);
							}else if( $res['type'] == 2 )//老师
							{
								$res['user_type'] = 3;
								unset($res['type']);
							}

							$data = Db::table('tp_user')->where('nankai_user_id', $res['nankai_user_id'])->find();

							if ( !$data )//还没有此用户
							{
								$tp_user_id = Db::table('tp_user')->insertGetId($res);
								
								if ($tp_user_id)
								{
									$info = Db::table('tp_user')->where('id', $tp_user_id)->find();
									session::set('home_info', $info);//原有登录功能有此session 记录日志用的
									session::set('home_user_id', $tp_user_id);
									cookie('user_type', $res['user_type']);
								}
							}else//已有此用户
							{
								session::set('home_info', $data);//原有登录功能有此session 记录日志用的
								session::set('home_user_id', $data['id']);
								cookie('user_type', $res['user_type']);
							}
						//进行做实验前的数据准备 结束

                        // 缓存访问权限
                        \Rbac::saveAccessList();
                        $this->redirect('admin/index/index');
                    }//已是再次登录 结束

                }else//南开老师、学生用统一身份认证登录去做实验
                {
                    if ( $res['teaching_number'] )
                    {
                        $res['nankai_user_id'] = $res['teaching_number'];
                        unset($res['teaching_number'], $res['student_number']);
                    }else if ( $res['student_number'] )
                    {
                        $res['nankai_user_id'] = $res['student_number'];
                        unset($res['teaching_number'], $res['student_number']);
                    }
                    
                    if ( $res['type'] == 1 )//学生
                    {
                        $res['user_type'] = 2;
                        unset($res['type']);
                    }else if( $res['type'] == 2 )//老师
                    {
                        $res['user_type'] = 3;
                        unset($res['type']);
                    }
                    
                    $data = Db::table('tp_user')->where('nankai_user_id', $res['nankai_user_id'])->find();
                    
                    if ( !$data )//还没有此用户
                    {
                        $tp_user_id = Db::table('tp_user')->insertGetId($res);
                        
                        if ($tp_user_id)
                        {
                            $info = Db::table('tp_user')->where('id', $tp_user_id)->find();
                            session::set('home_info', $info);//原有登录功能有此session 记录日志用的
                            session::set('home_user_id', $tp_user_id);
                            cookie('user_type', $res['user_type']);
                            $this->redirect('index/subject/examine');
                        }
                    }else//已有此用户
                    {
                        session::set('home_info', $data);//原有登录功能有此session 记录日志用的
                        session::set('home_user_id', $data['id']);
                        cookie('user_type', $res['user_type']);
                        $this->redirect('index/subject/examine');
                    }
                }//南开老师、学生用统一身份认证登录去做实验 结束
                
            }//try 结束
            catch (Exception $e)
            {
                //header("Location:" . $loginServer . "?service=" . $address); //无法访问
				$this->redirect( $loginServer . "?service=" . $address );
            }    
        }
        else
        {
            //header("Location:" . $loginServer . "?service=" . $address); //无法访问
			$this->redirect( $loginServer . "?service=" . $address );
		}
	}//check_user() 结束
}
