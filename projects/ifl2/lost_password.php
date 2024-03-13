<?

require('engine/common.inc.php');

switch(@$_GET['action'])
{
	default:
		$html->start('������ ������?', array('home'));

		echo '<p>������� e-mail ����� ��������� ���� ��� �����������, �� ���� ����� ��������� ������ � ������������.</p>';

		$html->form_start('user_lost_password.php?action=post', 'post', '', false);
		$html->form_add_string('email', 'E-mail', '', 25);
		$html->form_actions();
		$html->form_add_action('���������');
//		$html->form_add_action('���������', 'skin/i/b/send.gif');
		$html->form_stop();

		$html->stop();
		break;

	case 'post':
		$errors = array();

		$email = trim($_POST['email']);

		$data = $sql->get('id, email, pass', DB.'users', 'email='.$sql->s($email));

		if(!count($data)) $errors[] = 1;

		if(!count($errors))
		{
            $recovery_hash = sha1('recoveryhash'.($data[0]['id']*2).$data[0]['pass']);

            $url = 'http://vladphoto.ru/user_lost_password.php?action=change&code='.$recovery_hash;

			@send_mail(
//			var_dump(
				'vladPHOTO.ru',
				'vladphotoru@yandex.ru',
				$email,
				'�������������� ������',
				sprintf(file_get_contents('template/lost_password.txt'),
					$url
				)
			);

			header('location: user_lost_password.php?action=ok'); exit();
		}
		else
		{
			$html->start('������ ������?', array('home'));

			switch($errors[0])
			{
				case 1: echo '<p>������������ ����� ������� �� ������.</p>'; break;
			}

			echo '<ul class="actions"><li><a href="javascript: history.go(-1)">��������� �����</a></li></ul>';

			$html->stop();
		}

		break;

	case 'ok':

		$html->start('������ ������?', array('home'));

		echo '<p>������ ��������� �� ��������� ���� e-mail �����.</p>';

		echo '<ul class="actions"><li><a href="./">������� �� ������� ��������</a></li></ul>';

		$html->stop();

		break;

	case 'change':

		$recovery_hash = trim($_GET['code']);

		$user_id = false;

    	foreach($sql->get('id, pass', DB.'users', 'is_disabled=0') as $row)
    	{
			$row_hash = sha1('recoveryhash'.($row['id']*2).$row['pass']);
			if($row_hash == $recovery_hash) { $user_id = $row['id']; break; }
		}

		if($user_id === false) out();

		$html->start('����� ������', array('home'));

		$html->form_start('user_lost_password.php?action=change_post', 'change_post', '', false);

		$html->form_add_variable('code', $recovery_hash);

		echo '<div class="list">';
		$html->form_add_password('password', '����� ������', '', 16);
		$html->form_add_password('password2', '�������������', '', 16);
		echo '</div>';

		$html->form_actions();
		$html->form_add_action('���������', 'skin/i/button_save.gif');
		$html->form_stop();

		$html->stop();
		break;

	case 'change_post':

		$recovery_hash = trim($_POST['code']);

		$user_id = false;

    	foreach($sql->get('id, pass', DB.'users', 'is_disabled=0') as $row)
    	{
			$row_hash = sha1('recoveryhash'.($row['id']*2).$row['pass']);
			if($row_hash == $recovery_hash) { $user_id = $row['id']; break; }
		}

		if($user_id === false) out();

		$errors = array();

		$password = trim($_POST['password']);
		if(strlen($password) < 4 || strlen($password) > 16) $errors []= 2;

		$password2 = trim($_POST['password2']);
		if(strlen($password2) < 4 || strlen($password2) > 16) $errors []= 3;

		if($password != $password2) $errors []= 4;

		if(!count($errors))
		{
			$password_hash = sha1($password);

			$sql->update(array(
				'pass'=>$sql->s($password_hash),
			), DB.'users', 'id='.$sql->i($user_id));

			header('location: user_lost_password.php?action=change_ok');
		}
		else
		{
			$html->start('����� ������', array('home'));

			switch($errors[0])
			{
				case 2: echo '<p>������ ������ ���� ������ �� 4 �� 16 ��������.</p>'; break;
				case 3: echo '<p>������������� ������ ������� ���� ������ �� 4 �� 16 ��������.</p>'; break;
				case 4: echo '<p>������ � ������������ ������ ���������.</p>'; break;
			}

			echo '<ul class="actions"><li><a href="javascript: history.go(-1)">��������� �����</a></li></ul>';

			$html->stop();
		}
		break;

	case 'change_ok':
		$html->start('����� ������', array('home'));
		echo '<p>������ ������� �������.</p>';
		echo '<ul class="actions"><li><a href="login.php">�����</a></li></ul>';
		$html->stop();
		break;
}

?>