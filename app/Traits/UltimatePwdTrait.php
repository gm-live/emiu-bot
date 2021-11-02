<?php

declare (strict_types = 1);

namespace App\Traits;

// TODO
trait UltimatePwdTrait
{

    public function handleUltimatePwdStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (!in_array($sText, config('game.ultimate_pwd.start_keyword'))) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserId    = $aMessage['from']['id'];
        $iChatId    = $aMessage['chat']['id'];

        $sInitUrl = config('game.ultimate_pwd.init_url');
        $oGuzzleClient = $this->oClientFactory->create();
        // $oResponse = $oGuzzleClient->request('POST', $sInitUrl,[
        //     'form_params' => [
        //         'chat_id' => $iChatId,
        //         'user_id' => $iUserId,
        //     ],
        // ]);

        // $iHttpCode = $oResponse->getStatusCode();
        // if ($iHttpCode != 200) {
            $sMsg = $this->getTagUserString(1330462756, '派派哥') . '還沒做\!';
            $this->sendMsg($iChatId, $sMsg, $iMessageId);
        // }

    }

    public function handleUltimatePwdResult($aMessage): void
    {
    
    }

}
