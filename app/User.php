<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // User Can Have Many Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    // User Can Have Mant Policies
    public function policies()
    {
        return $this->hasMany(Policy::class);
    }
    // User Can Have Many Contracts
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    // User Can Upload MultipleInvoices
    public function userInvoices()
    {
        return $this->hasMany(UserInvoice::class);
    }
    // User Can Hav Many Attachments
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
    // User Claim Files
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
    // User have many details uploaded
    public function details()
    {
        return $this->hasMany(Detail::class);
    }
    // User Has Many Payment
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // User Has Many Renewals
    public function renewals()
    {
        return $this->hasMany(Renewal::class);
    }

}
