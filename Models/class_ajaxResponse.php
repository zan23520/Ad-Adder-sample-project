<?php

class ajaxResponse
{
    /**
     * Creates ajax call response JSON object
     * @param {boolean} success
     * @param {string} message
     * @param {objects} data -optional
     */
    public function createJson($success, $message, $data)
    {
        $returnData = [
            'success' => $success, // = true
            'msg' => $message //  = ""
        ];
    
        if(isset($data)) {
            $returnData['data'] = $data;
        }
    
        return json_encode($returnData);
    }
}