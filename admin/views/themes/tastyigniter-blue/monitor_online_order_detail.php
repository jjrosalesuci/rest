<table class="table table-condensed">
    <thead>
      <tr>
        <th>Name/Options</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>

    <?php foreach($cart_items as $item) { ?>

        <tr>
            <td>
              <div> <?php echo $item['name']?>                      </div>           
              <div  class="options">  <?php echo $item['options']?>           </div>   
              <div class="comments"> <b> <?php echo $item['comment']?></b>    </div>         
            </td>
            <td><?php echo $item['qty']?></td>
            <td><?php echo $item['price']?></td>
            <td><?php echo $item['subtotal']?></td>
        </tr>

      <?php } ?>

      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo $order_total?></td>
      </tr>
      
    </tbody>
  </table>