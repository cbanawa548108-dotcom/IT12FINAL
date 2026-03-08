@component('mail::message')
# Hello, {{ $fname }}!

Your one-time login verification code is:

@component('mail::panel')
# {{ $code }}
@endcomponent

This code is valid for **10 minutes**. Do not share it with anyone.

If you did not attempt to log in, please ignore this email.

Thanks,
**CRM FruitStand**
@endcomponent