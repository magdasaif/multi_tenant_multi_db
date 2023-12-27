<?php
namespace App\Trait;
use App\Models\Store;

trait BelongToStoreTrait 
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    //=======================================//
    // protected static function booted()
    // {
    //     static::addGlobalScope('store', function ($query) {
    //     $store=app()->make('store.active'); //return domain that accessed now 
    //     $query->where('store_id', $store->id);
    //     });
    // }
    //=======================================//
    /*
     معرفة بالشكل ده static دى فانكشن 
      boot +BelongToStoreTrait
      علشان الموديل اول ما يرن يتعرف عليها ع انها فانكشن booted 
      وف نفس الوقت لو فى فى الموديل فانكشن booted ميحلص للفانكشن الموجودة فى ال
      trait
       دى الغاء كانها مش موجودة 
       والحالة دى تستخدم مع ال
       trait
    */
    //=======================================//
    protected static function bootBelongToStoreTrait()
    {
        //bound function to check if stor.active found in service container or no 
        //to prevent error from make function
        if(app()->bound('store.active')){
            $store=app()->make('store.active'); 
            if(isset($store)){
                if(app()->make('store.active')!='not'){
                    static::addGlobalScope('store', function ($query) {
                        $store=app()->make('store.active'); //return domain that accessed now 
                        $query->where('store_id', $store->id);
                    });
                }
            }
        }
    }
}

?>
