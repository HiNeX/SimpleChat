<?php
/**
 * Chat controller.
 * Author: Roman Hinex
 * Date: 03.12.14
 * Time: 5:39
 */

namespace Controllers;


use Core\Base\Controller;
use Core\System\Session;
use Models\Dialogs;
use Models\Messages;
use Models\Users;

class ChatController extends Controller {
	protected $_user;

	public function runBefore() {
		$auth = $this->user->auth();
		if ($auth['success'] === false) {
			$this->request->redirect(['index', 'index']);
		}

		$this->_user = $auth['result']->getId();
	}

	public function createroomAction() {
		$to = (int) $this->request->getQuery('id');
		$user_id = Users::find($to)->getId();
		$dialogs = Dialogs::find([['from', '=', $to], ['to', '=', $this->_user]]);

		if (!$dialogs->getId() AND $user_id) {
			$dialogs = Dialogs::create();
			$dialogs->setFrom(intval($user_id));
			$dialogs->setTo($this->_user);
			$dialogs->setNew(0);
			$dialogs->save();

			$tw = Dialogs::create();
			$tw->setFrom($this->_user);
			$tw->setTo(intval($user_id));
			$tw->setNew(0);
			$tw->save();
		}

		$this->request->redirect(['chat', 'index', ['id' => $dialogs->getId()]]);
	}


	public function indexAction() {
		$this->display->setTitle('Online Chat!');

		$dialog = $this->request->getQuery('id');
		$dialogs = Dialogs::findAll([['to', '=', $this->_user]])->fetchAll();

		$id = NULL;

		if ($dialog) {
			$dialog = Dialogs::find($dialog);

			if ($id = $dialog->getId()) {
				$from = $dialog->getFrom();
				$to = $dialog->getTo();
				$dialog->setNew(0);
				$dialog->save();

				$dialogs_for = Dialogs::find([['from', '=', $to], ['to', '=', $from]]);
				$id_for = $dialogs_for->getId();

				if ($message_text = $this->request->getPost('message')) {
					$message = Messages::create();
					$message->setDialogId($id);
					$message->setUserId($this->_user);
					$message->setTime(date( 'Y-m-d H:i:s'));
					$message->setMessage(htmlspecialchars($message_text));
					$message->save();

					$dialogs_inc = Dialogs::find($id_for);
					$dialogs_inc->setNew($dialogs_inc->getNew() + 1);
					$dialogs_inc->save();

					$this->display->set('message', ['type' => 'success', 'text' => $this->translation->get('Message send')]);
				}

				$messages1 = Messages::findAll([['dialog_id', '=', $id]])->fetchAll();
				$messages2 = Messages::findAll([['dialog_id', '=', $id_for]])->fetchAll();

				$messages = array_merge($messages1, $messages2);
				asort($messages);

				$this->display->set('dialog', ['id' => $id, 'messages' => $messages]);
			}

		}



		$list = [];
		foreach ($dialogs as $dialog) {
			$user = Users::find($dialog['from']);
			$online = (strtotime($user->getOnline()) >= time()-300);
			$list[] = ['id' => $dialog['id'], 'nickname' => $user->getNickname(), 'online' => $online, 'new' => $dialog['new'], 'active' => ($id == $dialog['id'])];
		}

		$this->display->set('dialogs', $list);


		$this->display->render();
	}

	public function usersAction() {
		$users = Users::findAll();

		$this->display->setTitle('Online Chat!');
		$this->display->set('users', $users->fetchAll());
		$this->display->set('last', time() - 300);
		$this->display->render();
	}

	public function settingsAction() {

		if (isset($_POST['nickname']) AND preg_match("/^[a-zA-Z0-9]+$/", $_POST['nickname']) === 1) {
			$user = Users::find([['nickname', '=', $_POST['nickname']]]);

			if ($user->getId() AND $user->getId() !== $this->_user) {
				$this->display->set('message', ['type' => 'error', 'text' => $this->translation->get('Nickname exist')]);
			} else {
				$current = Users::find($this->_user);
				$current->setNickname($_POST['nickname']);
				$current->save();
			}
		}

		if (isset($_POST['password_old']) AND isset($_POST['password_new']) AND !empty($_POST['password_old']) AND !empty($_POST['password_new'])) {


			if ($this->user->change($_POST['password_old'], $_POST['password_new'])) {
				$message = ['type' => 'success', 'text' => 'Password success changed'];
			} else {
				$message = ['type' => 'error', 'text' => 'Invalid old password'];
			}

			$this->display->set('message', $message);
		}

		$current = Users::find($this->_user);

		$this->display->setTitle('Online Chat!');
		$this->display->set('nickname', $current->getNickname());
		$this->display->render();
	}

	public function exitAction() {
		if (isset($_POST['exit'])) {
			$session = Session::main();
			$session->remove('user_id');

			$user = Users::find($this->_user);
			$user->setToken('');
			$user->save();

			$this->request->redirect(['index', 'index']);
		}

		$this->display->setTitle('Online Chat!');
		$this->display->render();
	}
}