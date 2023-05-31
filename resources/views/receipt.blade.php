<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        .footer{
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height:100px;
        }
        
    </style>
</head>
<body style="background: white; padding: 10px">
 <table style="width: 100%">
    <tr >
        <td style="width: 50%"><h1 style="font-size: 20px">Receipt No. {{ $data['transaction_number'] }}</h1></td>
    </tr>
    <tr >
        <td style="width: 50%"><h1 style="font-size: 20px">Date: {{ date('Y-m-d') }}</h1></td>
    </tr>
 </table>
        <div style="margin-top: 5px">
            <table style="width: 80%">
                
                <tr>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Product</td>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Price</td>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Quantity</td>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Total</td>
                </tr>
                @foreach($point_of_sales as $pos)
                <tr>
                    <td style="padding: 5px; font-size: 20px">{{ $pos['product']['name'] }}</td>
                    <td style="padding: 5px; font-size: 20px">{{ $pos['product']['price'] }}</td>
                    <td style="padding: 5px; font-size: 20px">{{ $pos['quantity'] }}</td>
                    <td style="padding: 5px; font-size: 20px">{{ $pos['total'] }}</td>
                  </tr>
                @endforeach
              </table>

              <hr>
              <table style="width: 80%">
                
                <tr>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Cash: {{ $data['cash'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px;font-weight: bold; font-size: 20px">Change: {{ $data['change'] }}</td>
                </tr>
              </table>
        </div>
</body>
</html>
