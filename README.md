# cakephp3-uploadBehavior


This is a simple Behavior which ban be attached to a cakePHP3 Model/Table.
It will take uploaded file(s) and copy them to a configured upload folder.

# Using the Behavior

To use the behavior you have to add it to a model. As Models in cakePHP3 are divided into Table and Entity, you have to add it to a Table for example:

```
function initialize() 
{
    $this->addBehavior('Upload', [
        'field' => MYMODELFIELDHERE,
        'uploadPath' => MYUPLOADPATH
    ]);

}
```

The behavior is using callbacks beforeSave() and beforeDelete().

On beforeSave() it will move a file of target field to uploadPath, and set the models field with the path to that file.
It will also automatically delete an old file, if on update another file with a different name is uploaded.

On beforeDelete() it will delete files associated to a model entity destined for delete.



Please keep in mind that this is still work in progress.


