<?php
defined( 'ABSPATH' ) || exit;

$forms = \MetCraft\Core\Forms\Base::instance()->get_all_forms();
$total_forms = count( $forms );

$total_entries = 0;
$recent_entries = \MetCraft\Core\Entries\Base::instance()->get_entries();
$total_entries = count( $recent_entries );

$total_submissions = 0;
foreach ( $forms as $form ) {
    $stats = \MetCraft\Core\Analytics\Base::instance()->get_stats( $form['id'] );
    $total_submissions += $stats['submissions'];
}
?>
<div class="wrap metcraft-dashboard">
    <h1><?php esc_html_e( 'MetCraft Dashboard', 'metcraft' ); ?></h1>

    <div class="mcf-dashboard-cards" style="display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap;">
        <div class="mcf-dashboard-card" style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: #6b7280; margin-bottom: 8px;"><?php esc_html_e( 'Total Forms', 'metcraft' ); ?></div>
            <div style="font-size: 32px; font-weight: 700; color: #1f2937;"><?php echo esc_html( $total_forms ); ?></div>
        </div>
        <div class="mcf-dashboard-card" style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: #6b7280; margin-bottom: 8px;"><?php esc_html_e( 'Total Entries', 'metcraft' ); ?></div>
            <div style="font-size: 32px; font-weight: 700; color: #1f2937;"><?php echo esc_html( $total_entries ); ?></div>
        </div>
        <div class="mcf-dashboard-card" style="flex: 1; min-width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: #6b7280; margin-bottom: 8px;"><?php esc_html_e( 'Total Submissions', 'metcraft' ); ?></div>
            <div style="font-size: 32px; font-weight: 700; color: #1f2937;"><?php echo esc_html( $total_submissions ); ?></div>
        </div>
    </div>

    <div class="mcf-dashboard-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="mcf-admin-card">
            <h2><?php esc_html_e( 'Recent Forms', 'metcraft' ); ?></h2>
            <a href="<?php echo esc_url( admin_url( 'post.php?post=0&action=edit&post_type=metcraft-form' ) ); ?>" class="mcf-admin-btn" style="float: right;"><?php esc_html_e( 'Create New Form', 'metcraft' ); ?></a>
            <div style="clear: both;"></div>
            <table class="mcf-admin-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Title', 'metcraft' ); ?></th>
                        <th><?php esc_html_e( 'Entries', 'metcraft' ); ?></th>
                        <th><?php esc_html_e( 'Modified', 'metcraft' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_slice( $forms, 0, 5 ) as $form ) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $form['id'] . '&action=edit' ) ); ?>"><?php echo esc_html( $form['title'] ); ?></a></td>
                            <td><?php echo esc_html( count( \MetCraft\Core\Entries\Base::instance()->get_entries( $form['id'] ) ) ); ?></td>
                            <td><?php echo esc_html( $form['date'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ( empty( $forms ) ) : ?>
                        <tr><td colspan="3"><?php esc_html_e( 'No forms yet. Create your first form.', 'metcraft' ); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mcf-admin-card">
            <h2><?php esc_html_e( 'Recent Entries', 'metcraft' ); ?></h2>
            <table class="mcf-admin-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Entry', 'metcraft' ); ?></th>
                        <th><?php esc_html_e( 'Form', 'metcraft' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'metcraft' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_slice( $recent_entries, 0, 5 ) as $entry ) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $entry['id'] . '&action=edit' ) ); ?>"><?php echo esc_html( $entry['title'] ); ?></a></td>
                            <td><?php echo esc_html( $entry['form_id'] ); ?></td>
                            <td><?php echo esc_html( $entry['date'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ( empty( $recent_entries ) ) : ?>
                        <tr><td colspan="3"><?php esc_html_e( 'No entries yet.', 'metcraft' ); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
