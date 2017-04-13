<?php

/**
 * Created by PhpStorm.
 * User: alderzhang
 * Date: 2017/4/13
 * Time: 09:27
 */
require_once dirname(__FILE__) . '/../../Config.php';

require_once SERVICE_PATH . '/TokenCmd.php';
require_once SERVICE_PATH . '/CmdResp.php';
require_once ROOT_PATH . '/ErrorNo.php';
require_once MODEL_PATH . '/AvRoom.php';

class GetLinkSigCmd extends TokenCmd
{
    private $avRoom;

    public function parseInput()
    {
        if (empty($this->req['id']))
        {
            return new CmdResp(ERR_REQ_DATA, 'Lack of id');
        }
        if (!is_string($this->req['id']))
        {
            return new CmdResp(ERR_REQ_DATA, 'Invalid id');
        }

        if (!isset($this->req['roomnum']))
        {
            return new CmdResp(ERR_REQ_DATA, 'Lack of roomnum');
        }
        if (!is_int($this->req['roomnum']))
        {
            return new CmdResp(ERR_REQ_DATA, ' Invalid roomnum');
        }

        $this->avRoom = new AvRoom($this->user);

        return new CmdResp(ERR_SUCCESS, '');
    }

    public function handle()
    {
        $linkSig = '';
        $errorMsg = '';

        $ret = $this->avRoom->load();
        if ($ret < 0)// 加载房间出错
        {
            return new CmdResp(ERR_SERVER, 'User av room not exists');
        }

        $ret = $this->avRoom->getLinkSig($this->req['id'], $this->req['roomnum'], $linkSig, $errorMsg);
        if($ret != ERR_SUCCESS)
        {
            return new CmdResp($ret, $errorMsg);
        }

        $data = array(
            'linksig' => $linkSig,
        );
        return new CmdResp(ERR_SUCCESS, '', $data);
    }
}