<div class="row">
    <?php if( isset($_SESSION['cart']['items']) && count($_SESSION['cart']['items']) > 0): ?>
    <form class="form-inline" method="post">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">SERIAL</th>
                        <th class="text-center">IMAGE</th>
                        <th>PRODUCT NAME</th>
                        <th>PRICE</th>
                        <th>QUANTITY</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($_SESSION['cart']['items'] as $prid => $item): ?>
                    <tr>
                        <td><a href="<?php echo add_query_arg(['remove_cart' =>$prid], home_url()); ?>" class="remove-cart-item">Remove</a></td>
                        <td class="text-center"><?= $i ?></td>
                        <td width="100"><img src="<?php echo $item['image']; ?>" style="max-width: 100px;" class="img-responsive" alt="<?php echo $item['name']; ?>" /></td>
                        <td>
                            <strong><?php echo $item['name']; ?></strong>
                        </td>
                        <td><?php echo $item['price']; ?></td>
                        <td style="width: 50px;"><input style="width: 100%;" type="number" class="form-control" name="quantity[<?php echo $prid; ?>]" value="<?php echo $item['quantity']; ?>" /></td>
                        <td><?= (double)$item['price'] * (int)$item['quantity'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-3 cart-bottom text-center">SUBTOTAL: 0</div>
        <div class="col-md-3 cart-bottom text-center">SHIPPING: 0</div>
        <div class="col-md-3 cart-bottom text-center">TAX: 0</div>
        <div class="col-md-3 cart-bottom text-center">TOTAL: 0</div>
        <div class="text-right">
            <input type="submit" name="CONTINUE" class="btn btn-default" value="CONTINUE" />
            <input type="submit" name="DELETE_ALL" class="btn btn-default" value="DELETE ALL" />
            <input type="submit" name="update_cart" class="btn btn-default" value="UPDATE" />
            <input type="submit" name="checkout" class="btn btn-danger" value="CHECKOUT" />
        </div>
    </div>
    </form>
    <?php else: ?>
        <h3>Don't have any products in cart!</h3>
    <?php endif; ?>
</div>