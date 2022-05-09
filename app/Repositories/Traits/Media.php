<?php

namespace App\Repositories\Traits;

use Illuminate\Http\Request;

trait Media
{
    /**
     * Helper method to upload media for any model
     */
    public function uploadMedia(Request $request)
    {
        $files = $request->file('files');

        $responses = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                $responses[] = $file->storeOnCloudinary('commerce')->getSecurePath();
            }
        } else {
            $responses = $files->storeOnCloudinary('commerce')->getSecurePath();
        }


        return $responses;
    }
}
