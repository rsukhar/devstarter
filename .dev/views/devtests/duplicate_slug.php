<div class="b-main">
	<section class="b-section">
		<div class="b-section-h">

			<div class="b-content">
				<table>
					<tbody>
						<?php foreach ($data as $slug => $ids):?>
							<tr>
								<td><strong>slug : <?php echo $slug?></strong></td>
                                <td style="display: flex;">
									<?php foreach ($ids as $id => $values):?>
                                        <div style="display: flex;border: 1px solid;margin-right: .5rem;flex-wrap: wrap;justify-content: center;">
                                            <div style="width: 100%;text-align: center;">
                                                <strong>trim_id : <?php echo $id?></strong><br>
                                                <strong>title : <?php echo Arr::path($meta_data, $id.'.title');?></strong><br>
                                                <strong>year_start : <?php echo Arr::path($meta_data, $id.'.year_start');?></strong><br>
                                                <strong>year_end : <?php echo Arr::path($meta_data, $id.'.year_end');?></strong><br>
                                            </div>
                                            <?php foreach ($values as $item):?>
                                                <div style="display: flex;flex-direction: column;font-size: 14px; margin-right: .5rem;">
													<?php foreach ($item as $key => $value):?>
                                                    <span><?php echo $key ?>=<?php echo $value?></span>
													<?php endforeach;?>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
									<?php endforeach;?>
                                </td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>