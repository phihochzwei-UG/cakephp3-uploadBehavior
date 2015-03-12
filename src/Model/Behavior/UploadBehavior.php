<?php
/**
 * Created by IntelliJ IDEA.
 * User: sebastiankoller
 * Date: 10.03.15
 * Time: 12:00
 */

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class UploadBehavior extends Behavior {

    protected $_defaultConfig = [
        'field' => 'img',
        'uploadPath' => 'uploads/advertisements/'
    ];


    /**
     * get appropriate fields with files
     * move uploaded file and set path to entity field, but only if a file was selected
     *
     * @param Entity $entity
     */
    public function uploadFile(Entity $entity)
    {
        $config = $this->config();
        if (!is_array($config['field'])) {
            $field = $entity->get($config['field']);
            if (empty($field['tmp_name'])) {
                $entity->unsetProperty($config['field']);
            } else {

                if ($originalFilePath = $entity->getOriginal('img')) {
                    $this->_delete($originalFilePath);
                }
                $filePath = $this->_moveFile($field);
                $entity->set($config['field'], $filePath);
            }

        } else {
            foreach ($config['field'] AS $value) {
                $field = $entity->get($value);
                if (empty($field['tmp_name'])) {
                    $entity->unsetProperty($config['field']);
                } else {

                    if ($originalFilePath = $entity->getOriginal('img')) {
                        $this->_delete($originalFilePath);
                    }
                    $filePath = $this->_moveField($field);
                    $entity->set($config['field'], $filePath);
                }
            }
        }

    }

    private function _moveFile($uploadField)
    {
        $uploadPath = $this->config('uploadPath');
        $uploadFolder = new Folder(ROOT.DS.'webroot'.DS.$uploadPath, true, 0755);

        $upload = "";
        if (isset($uploadField['tmp_name'])) {
            $upload = $uploadPath.$uploadField['name'];
            move_uploaded_file($uploadField['tmp_name'], $uploadPath.DS.$uploadField['name']);
        }

        return $upload;
    }

    /**
     * try to delete given file path
     *
     * @param $file
     * @return bool
     */
    public function deleteFile($entity)
    {
        $config = $this->config();
        if (!is_array($config['field'])) {
            $filePath = $entity->get($config['field']);
            $this->_delete($filePath);
        } else {
            foreach ($config['field'] AS $value) {
                $filePath = $entity->get($value);
                $this->_delete($filePath);
            }
        }
    }

    private function _delete($filePath)
    {
        $fileObject = new File($filePath);
        return $fileObject->delete();
    }





    public function beforeSave(Event $event, Entity $entity)
    {
        $this->uploadFile($entity);
    }

    public function beforeDelete(Event $event, Entity $entity)
    {
        $this->deleteFile($entity);
    }

}
