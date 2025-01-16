@php
    $subtotal = Cart::instance('pos_shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $shipping = Session::get('pos_shipping');
    $total_discount = Session::get('pos_discount') + Session::get('product_discount');
    $paid = Session::get('cpaid') ? Session::get('cpaid') : 0;
    $old_due = Session::get('old_due') ?? 0;
    $additional_shipping = Session::get('additional_shipping') ?? 0;
@endphp
<tr>
    <td>Sub Total</td>
    <td>{{ $subtotal }}</td>
</tr>
<tr>
    <td>Shipping Fee</td>
    <td>{{ $shipping + $additional_shipping }}</td>
</tr>
<tr>
    <td>Discount</td>
    <td>{{ $total_discount }}</td>
</tr>
<tr>
    <td>Paid</td>
    <td>{{ $paid }}</td>
</tr>
<tr>
    <td>Old Due</td>
    <td>{{ $old_due }}</td>
</tr>
<tr>
    <td>Due Bill</td>
    <td>{{ $subtotal + $shipping + $additional_shipping + $old_due - ($total_discount + $paid) }}</td>
</tr>
