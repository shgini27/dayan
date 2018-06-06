<select name="roomid" class="form-control" id="hostel-select">
                          <option value="0"><?php echo lang("ctn_356") ?> ...</option>
                          <?php foreach($rooms->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                          <?php endforeach; ?>
                        </select>