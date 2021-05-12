<?php
/**
 * @var array $students
 */
?>
	<div class="b-titlebar">
		<div class="b-titlebar-h">
			<h1>Ученики</h1>
			<a class="g-btn style_solid color_green" href="/admin/students/create/">Добавить ученика</a>
		</div>
	</div>
	<div class="b-main">
	<section class="b-section">
		<div class="b-section-h">
			<?php if ( ! empty($students)):?>
				<table class="g-table for_shops">
					<thead>
					<tr>
						<td class="for_first_name">Имя</td>
						<td class="for_last_name">Фамилия</td>
						<td class="for_birthday">День рождения</td>
						<td class="for_phone">Телефон</td>
						<td class="for_actions"></td>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($students as $student): ?>
						<tr>
							<td class="for_first_name"><?php echo HTML::chars($student['first_name']) ?></td>
							<td class="for_last_name"><?php echo HTML::chars($student['last_name']) ?></td>
							<td class="for_birthday"><?php echo date('d.m.Y', strtotime($student['birthday'])) ?></td>
							<td class="for_phone"><?php echo HTML::chars($student['phone']) ?></td>
							<td class="for_actions">
								<a class="g-action type_icon action_edit" href="/admin/students/<?php echo $student['id'] ?>/update/"></a>
								<span class="g-action type_icon action_delete" data-nonce="<?php echo Security::nonce('delete_student')?>"></span>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<div class="b-results right">
					<?php if (isset($pagination) AND ! empty($pagination)): ?>
						<?php echo $pagination; ?>
					<?php else: ?>
						<div class="g-pagination"></div>
					<?php endif; ?>
				</div>
			<?php endif;?>
		</div>
	</section>
	</div><?php
