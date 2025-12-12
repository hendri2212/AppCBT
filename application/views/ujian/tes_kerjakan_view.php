<!-- CSS Anti Copy-Paste & Selection -->
<style>
    /* Disable text selection on exam content */
    .no-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
    }
    
    /* Disable image dragging */
    .no-select img {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
        pointer-events: none;
    }
    
    /* Prevent text selection highlight */
    .no-select::selection {
        background: transparent;
    }
    .no-select::-moz-selection {
        background: transparent;
    }
</style>

<div class="container no-select">
	<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tes : <?php if(!empty($tes_name)){ echo $tes_name; } ?>
        </h1>
        <div class="breadcrumb">
            <img src="<?php echo base_url(); ?>public/images/zoom.png" style="cursor: pointer;" height="20" onclick="zoomnormal()" title="Klik ukuran font normal" />&nbsp;&nbsp;
            <img src="<?php echo base_url(); ?>public/images/zoom.png" style="cursor: pointer;" height="26" onclick="zoombesar()" title="Klik ukuran font lebih besar" />
        </div>
    </section>

	<!-- Main content -->
    <section class="content">
    	<div class="row">
        <?php echo form_open('tes_kerjakan/simpan_jawaban','id="form-kerjakan"')?>
            <input type="hidden" name="tes-id" id="tes-id" value="<?php if(!empty($tes_id)){ echo $tes_id; } ?>">
            <input type="hidden" name="tes-user-id" id="tes-user-id" value="<?php if(!empty($tes_user_id)){ echo $tes_user_id; } ?>">
            <input type="hidden" name="tes-soal-id" id="tes-soal-id" value="<?php if(!empty($tes_soal_id)){ echo $tes_soal_id; } ?>">
            <input type="hidden" name="tes-soal-nomor" id="tes-soal-nomor"  value="<?php if(!empty($tes_soal_nomor)){ echo $tes_soal_nomor; } ?>">
            <input type="hidden" name="tes-soal-jml" id="tes-soal-jml" value="<?php if(!empty($tes_soal_jml)){ echo $tes_soal_jml; } ?>">
            <input type="hidden" name="tes-soal-ragu" id="tes-soal-ragu" value="<?php if(!empty($tes_ragu)){ echo $tes_ragu; } ?>">
    		<div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Soal <span id="judul-soal"><?php if(!empty($tes_soal_nomor)){ echo 'ke '.$tes_soal_nomor; } ?></span></h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right">
                            <div id="sisa-waktu"></div>
                        </div>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div id="isi-tes-soal" style="font-size: 15px;">
                        <?php if(!empty($tes_soal)){ echo $tes_soal; } ?>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="button" class="btn btn-default hide" id="btn-sebelumnya">Soal Sebelumnya</button>&nbsp;&nbsp;&nbsp;
                    <div class="btn btn-warning" id="btn-ragu" onclick="ragu()">
                        <input type="checkbox" style="width:10px;height:10px;" name="btn-ragu-checkbox" id="btn-ragu-checkbox" <?php if(!empty($tes_ragu)){ echo "checked"; } ?> /> Ragu-ragu
                    </div>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-default" id="btn-selanjutnya">Soal Selanjutnya</button>
                </div>
            </div><!-- /.box -->
        </form>
    	</div>
        <div class="row">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Soal</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <?php if(!empty($tes_daftar_soal)){ echo $tes_daftar_soal; } ?>
                    <p class="help-block">Soal yang sudah dijawab akan berwarna Biru.</p>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button class="btn btn-default pull-right" id="btn-hentikan">Hentikan Tes</button>
                </div>
            </div><!-- /.box -->
        </div>
    </section><!-- /.content -->

    <div class="modal" style="max-height: 100%;overflow-y: auto;" id="modal-hentikan" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <?php echo form_open($url.'/hentikan_tes','id="form-hentikan"'); ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <div id="trx-judul">Konfirmasi Hentikan Tes</div>
                </div>
                <div class="modal-body" >
                    <div class="row-fluid">
                        <div class="box-body">
                            <div id="form-pesan"></div>
                            <div class="callout callout-info">
                                <p>Apakah anda yakin mengakhiri mata uji ini ?
								<br />Jawaban Tes yang sudah selesai tidak dapat diubah.
								</p>
								
                            </div>
                            <div class="form-group">
                                <label>Nama Tes</label>
                                <input type="hidden" name="hentikan-tes-id" id="hentikan-tes-id" >
                                <input type="hidden" name="hentikan-tes-user-id" id="hentikan-tes-user-id" >
                                <input type="text" class="form-control" id="hentikan-tes-nama" name="hentikan-tes-nama" readonly>
                            </div>

                            <div class="form-group">
                                <label>Keterangan Soal</label>
                                <input type="text" class="form-control" id="hentikan-dijawab" name="hentikan-dijawab" readonly>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="hentikan-centang" name="hentikan-centang" value="1"> Centang dan klik tombol Hentikan Tes.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="box-footer">
					<button type="submit" id="tambah-simpan" class="btn btn-primary">Hentikan Tes</button>
					<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
				</div>
            </div>
        </div>

    </form>
    </div>

    <!-- Modal Warning Tab Switch -->
    <div class="modal" id="modal-warning-tabswitch" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="warningModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <div id="warning-judul"><i class="fa fa-exclamation-triangle"></i> PERINGATAN!</div>
                </div>
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="box-body text-center">
                            <h4><i class="fa fa-eye text-red"></i> Anda Terdeteksi Meninggalkan Halaman Ujian!</h4>
                            <p class="text-muted">Membuka tab lain atau aplikasi lain saat ujian berlangsung tidak diperbolehkan.</p>
                            <hr>
                            <p><strong>Total Pelanggaran: <span id="warning-violation-count" class="text-red">0</span> kali</strong></p>
                            <p class="text-muted small">Setiap pelanggaran akan dicatat dan dilaporkan kepada pengawas ujian.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Saya Mengerti, Lanjutkan Ujian</button>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.container -->

<script type="text/javascript">
    function zoombesar(){
        $('#isi-tes-soal').css("font-size", "140%");
        $('#isi-tes-soal').css("line-height", "140%");
    }

    function zoomnormal(){
        $('#isi-tes-soal').css("font-size", "15px");
        $('#isi-tes-soal').css("line-height", "110%");
    }

    function ragu(){
        $("#modal-proses").modal('show');

        $.ajax({
            url:'<?php echo site_url().'/'.$url; ?>/get_tes_soal_by_tessoal/'+$('#tes-soal-id').val(),
            type:"POST",
            cache: false,
            timeout: 10000,
            success:function(respon){
                var data = $.parseJSON(respon);
                if(data.data==1){
                    // Mengubah nilai ragu-ragu di database
                    if($('#tes-soal-ragu').val()==0){
                        var ragu=1;
                    }else{
                        var ragu=0;
                    }
                    $.ajax({
                            url:'<?php echo site_url().'/'.$url; ?>/update_tes_soal_ragu/'+$('#tes-soal-id').val()+'/'+ragu,
                            type:"POST",
                            cache: false,
                            timeout: 5000,
                            success:function(respon){
                                var data = $.parseJSON(respon);
                                if(data.data==1){
                                    notify_success('Jawaban Ragu-ragu berhasil diubah');
                                }
                            },
                            error: function(xmlhttprequest, textstatus, message) {
                                if(textstatus==="timeout") {
                                    $("#modal-proses").modal('hide');
                                    notify_error("Gagal mengubah Soal, Silahkan Refresh Halaman");
                                }else{
                                    $("#modal-proses").modal('hide');
                                    notify_error(textstatus);
                                }
                            }
                    });

                    // Mengubah warna daftar soal dan checkbox pada tombol ragu-ragu
                    if(data.tessoal_dikerjakan==1){
                        if($('#tes-soal-ragu').val()==0){
                            // Membuat menjadi ragu-ragu
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).removeClass('btn-primary');
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).addClass('btn-warning');
                            $('#btn-ragu-checkbox').prop("checked", true);
                            $('#tes-soal-ragu').val(1);
                        }else{
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).removeClass('btn-warning');
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).addClass('btn-primary');
                            $('#btn-ragu-checkbox').prop("checked", false);
                            $('#tes-soal-ragu').val(0);
                        }
                    }else{
                        if($('#tes-soal-ragu').val()==0){
                            // Membuat menjadi ragu-ragu
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).removeClass('btn-default');
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).addClass('btn-warning');
                            $('#btn-ragu-checkbox').prop("checked", true);
                            $('#tes-soal-ragu').val(1);
                        }else{
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).removeClass('btn-warning');
                            $('#btn-soal-'+$('#tes-soal-nomor').val()).addClass('btn-default');
                            $('#btn-ragu-checkbox').prop("checked", false);
                            $('#tes-soal-ragu').val(0);
                        }
                    }
                }
                $("#modal-proses").modal('hide');
            },
            error: function(xmlhttprequest, textstatus, message) {
                if(textstatus==="timeout") {
                    $("#modal-proses").modal('hide');
                    notify_error("Gagal mengubah soal, Silahkan Refresh Halaman");
                }else{
                    $("#modal-proses").modal('hide');
                    notify_error(textstatus);
                }
            }
        });
    }

    function soal(tessoal_id){
        $("#modal-proses").modal('show');
        $.ajax({
            url:'<?php echo site_url().'/'.$url; ?>/get_soal_by_tessoal/'+tessoal_id+'/'+$('#tes-user-id').val(),
            type:"POST",
            cache: false,
            timeout: 10000,
            success:function(respon){
                var data = $.parseJSON(respon);
                if(data.data==1){
                    $('#tes-soal-id').val(data.tes_soal_id);
                    $('#tes-soal-nomor').val(data.tes_soal_nomor);
                    $('#isi-tes-soal').html(data.tes_soal);
                    $('#tes-soal-ragu').val(data.tes_ragu);
                    $('#judul-soal').html('ke '+data.tes_soal_nomor);

                    if(data.tes_ragu==0){
                        // Menghilangkan checkbox ragu-ragu
                        $('#btn-ragu-checkbox').prop("checked", false);
                    }else{
                        // Menambah checkbox ragu-ragu
                        $('#btn-ragu-checkbox').prop("checked", true);
                    }

                    // menghilangkan tombol sebelum jika soal di nomor1
                    // dan menghilangkan tombol selanjutnya jika disoal terakhir
                    var tes_soal_nomor = parseInt($('#tes-soal-nomor').val());
                    var tes_soal_jml = parseInt($('#tes-soal-jml').val());
                    var tes_soal_tujuan = data.tes_soal_nomor;
                    if(tes_soal_tujuan==1){
                        $('#btn-sebelumnya').addClass('hide');
                        $('#btn-selanjutnya').removeClass('hide');
                    }else if(tes_soal_tujuan==tes_soal_jml){
                        $('#btn-sebelumnya').removeClass('hide');
                        $('#btn-selanjutnya').addClass('hide');
                    }else{
                        $('#btn-sebelumnya').removeClass('hide');
                        $('#btn-selanjutnya').removeClass('hide');
                    }

                }else if(data.data==2){
                    window.location.reload();
                }
                $("#modal-proses").modal('hide');
            },
            error: function(xmlhttprequest, textstatus, message) {
                if(textstatus==="timeout") {
                    $("#modal-proses").modal('hide');
                    notify_error("Gagal mengambil Soal, Silahkan Refresh Halaman");
                }else{
                    $("#modal-proses").modal('hide');
                    notify_error(textstatus);
                }
            }
        });
    }

    function audio(status){
        var audio_player_status = $('#audio-player-status').val();
        var audio_player_update = $('#audio-player-update').val();
        if(status==1){
            if(audio_player_update==0){
                $('#audio-player-update').val('1');
                /**
                 * Update status audio jika pemutaran audio dibatasi
                 */
                $.getJSON('<?php echo site_url().'/'.$url; ?>/update_status_audio/'+$('#tes-soal-id').val(), function(data){
                    if(data.data==1){
                        notify_success(data.pesan);
                    }
                });
            }
        }
        
        if(audio_player_status==0){
            $('#audio-player-status').val('1');
            $('#audio-player').trigger('play');
            $('#audio-player-judul').html('Pause');
            $('#audio-player-judul-logo').removeClass('fa-play');
            $('#audio-player-judul-logo').addClass('fa-pause');
        }else{
            $('#audio-player-status').val('0');
            $('#audio-player').trigger('pause');
            $('#audio-player-judul').html('Play');
            $('#audio-player-judul-logo').removeClass('fa-pause');
            $('#audio-player-judul-logo').addClass('fa-play');
        }
    }

    function audio_ended(status){
        if(status==1){
            $('#audio-control').addClass('hide');
        }else{
            $('#audio-player-status').val('0');
            $('#audio-player-judul').html('Play');
            $('#audio-player-judul-logo').removeClass('fa-pause');
            $('#audio-player-judul-logo').addClass('fa-play');
        }
    }

    function jawab(){
        $('#form-kerjakan').submit();
    }

    function hentikan_tes(){
        $("#modal-proses").modal('show');
        $('#hentikan-centang').prop("checked", false);
        $.getJSON('<?php echo site_url().'/'.$url; ?>/get_tes_info/'+$('#tes-id').val(), function(data){
            if(data.data==1){
                $('#hentikan-tes-id').val(data.tes_id);
                $('#hentikan-tes-user-id').val(data.tes_user_id);
                $('#hentikan-tes-nama').val(data.tes_nama);
                $('#hentikan-dijawab').val(data.tes_dijawab+" dijawab. "+data.tes_blum_dijawab+" belum dijawab.");
                $('#hentikan-belum-dijawab').val(data.tes_blum_dijawab);


                $("#modal-hentikan").modal('show');
            }else{
                window.location.reload();
            }
            $("#modal-proses").modal('hide');
        });
    }

    function soal_navigasi(navigasi){
        var tes_soal_nomor = parseInt($('#tes-soal-nomor').val());
        var tes_soal_jml = parseInt($('#tes-soal-jml').val());
        var tes_soal_tujuan = tes_soal_nomor+navigasi;

        if((tes_soal_tujuan>=1 && tes_soal_tujuan<=tes_soal_jml)){
            $('#btn-soal-'+tes_soal_tujuan).trigger('click');
        }
    }

    $(function () {
        var sisa_detik = <?php if(!empty($detik_sisa)){ echo $detik_sisa; } ?>;
        setInterval(function() {
            var sisa_menit = Math.round(sisa_detik/60);
            sisa_detik = sisa_detik-1;
            $("#sisa-waktu").html("Sisa Waktu : "+sisa_menit+" menit");

            if(sisa_detik<1){
                window.location.reload();
            }
        }, 1000);

        $('#btn-sebelumnya').click(function(){
            soal_navigasi(-1);
        });

        $('#btn-selanjutnya').click(function(){
            soal_navigasi(1);
        });

        $('#btn-hentikan').click(function(){
            hentikan_tes();
        });
        /**
         * Submit form soal saat sudah menjawab
         */
        $('#form-kerjakan').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/simpan_jawaban",
                    type:"POST",
                    data:$('#form-kerjakan').serialize(),
                    cache: false,
                    timeout: 10000,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            $("#modal-proses").modal('hide');
                            notify_success(obj.pesan);
                            $('#btn-soal-'+obj.nomor_soal).removeClass('btn-default');
                            $('#btn-soal-'+obj.nomor_soal).removeClass('btn-warning');
                            $('#btn-soal-'+obj.nomor_soal).addClass('btn-primary');
                        }else if(obj.status==2){
                            window.location.reload();
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(obj.pesan);
                        }
                    },
                    error: function(xmlhttprequest, textstatus, message) {
                        if(textstatus==="timeout") {
                            $("#modal-proses").modal('hide');
                            notify_error("Gagal menyimpan jawaban, Silahkan Refresh Halaman");
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(textstatus);
                        }
                    }
            });
            return false;
        });

        /**
         * Submit form hentikan tes
         */
        $('#form-hentikan').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/hentikan_tes",
                    type:"POST",
                    data:$('#form-hentikan').serialize(),
                    cache: false,
                    timeout: 10000,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            window.location.reload();
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(obj.pesan);
                        }
                    },
                    error: function(xmlhttprequest, textstatus, message) {
                        if(textstatus==="timeout") {
                            $("#modal-proses").modal('hide');
                            notify_error("Gagal menghentikan Tes, Silahkan Refresh Halaman");
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(textstatus);
                        }
                    }
            });
            return false;
        });

        $( document ).ready(function() {
            /**
             * ======================================
             * ANTI COPY-PASTE & SCREENSHOT PROTECTION
             * ======================================
             */
            
            // Disable right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                notify_error('Klik kanan tidak diperbolehkan selama ujian!');
                return false;
            });

            // Disable keyboard shortcuts for copy, paste, cut, select all, print
            document.addEventListener('keydown', function(e) {
                // Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+A, Ctrl+P, Ctrl+S
                if (e.ctrlKey && (
                    e.key === 'c' || e.key === 'C' ||
                    e.key === 'v' || e.key === 'V' ||
                    e.key === 'x' || e.key === 'X' ||
                    e.key === 'a' || e.key === 'A' ||
                    e.key === 'p' || e.key === 'P' ||
                    e.key === 's' || e.key === 'S'
                )) {
                    e.preventDefault();
                    notify_error('Shortcut keyboard tidak diperbolehkan selama ujian!');
                    return false;
                }
                
                // PrintScreen key
                if (e.key === 'PrintScreen') {
                    e.preventDefault();
                    notify_error('Screenshot tidak diperbolehkan selama ujian!');
                    // Clear clipboard as additional measure
                    navigator.clipboard.writeText('').catch(function(){});
                    return false;
                }
                
                // F12 (DevTools)
                if (e.key === 'F12') {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+Shift+I (DevTools)
                if (e.ctrlKey && e.shiftKey && (e.key === 'i' || e.key === 'I')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Disable copy event
            document.addEventListener('copy', function(e) {
                e.preventDefault();
                notify_error('Copy tidak diperbolehkan selama ujian!');
                return false;
            });

            // Disable cut event
            document.addEventListener('cut', function(e) {
                e.preventDefault();
                notify_error('Cut tidak diperbolehkan selama ujian!');
                return false;
            });

            // Disable paste event (except in textarea for essay answers)
            document.addEventListener('paste', function(e) {
                var target = e.target;
                // Allow paste only in textarea for essay answers
                if (target.tagName !== 'TEXTAREA' && target.id !== 'soal-jawaban') {
                    e.preventDefault();
                    notify_error('Paste tidak diperbolehkan selama ujian!');
                    return false;
                }
            });

            // Disable drag events
            document.addEventListener('dragstart', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable drop events
            document.addEventListener('drop', function(e) {
                e.preventDefault();
                return false;
            });

            /**
             * ======================================
             * TAB SWITCH DETECTION (existing code)
             * ======================================
             */
            
            // Inisialisasi counter pelanggaran
            var tabSwitchCount = 0;
            var isPageHidden = false;
            var tesUserId = $('#tes-user-id').val();

            /**
             * Function untuk mencatat pelanggaran tab switch ke server
             */
            function logTabSwitch(violationType) {
                $.ajax({
                    url: '<?php echo site_url().'/'.$url; ?>/log_tab_switch',
                    type: 'POST',
                    data: {
                        tesuser_id: tesUserId,
                        violation_type: violationType
                    },
                    cache: false,
                    success: function(respon) {
                        var data = $.parseJSON(respon);
                        if(data.data == 1) {
                            tabSwitchCount = data.total_violations;
                        }
                    }
                });
            }

            /**
             * Function untuk menampilkan warning modal
             */
            function showTabWarning() {
                $('#warning-violation-count').text(tabSwitchCount);
                $('#modal-warning-tabswitch').modal('show');
            }

            /**
             * Page Visibility API - Deteksi ketika user switch tab
             */
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // User meninggalkan tab (switch ke tab lain)
                    isPageHidden = true;
                    logTabSwitch('tab_switch');
                } else {
                    // User kembali ke tab ujian
                    if (isPageHidden) {
                        isPageHidden = false;
                        // Update counter dari response AJAX sebelumnya, lalu tampilkan warning
                        setTimeout(function() {
                            showTabWarning();
                        }, 300);
                    }
                }
            });

            /**
             * Window blur event - Deteksi ketika window kehilangan fokus (alt+tab, minimize, dll)
             * Hanya trigger jika bukan karena visibility change
             */
            var blurTimeout;
            window.addEventListener('blur', function() {
                // Hindari double counting dengan visibilitychange
                if (!document.hidden) {
                    blurTimeout = setTimeout(function() {
                        logTabSwitch('window_blur');
                    }, 100);
                }
            });

            window.addEventListener('focus', function() {
                // Clear timeout jika focus kembali dengan cepat
                clearTimeout(blurTimeout);
                // Tampilkan warning jika ada pelanggaran
                if (tabSwitchCount > 0 && !$('#modal-warning-tabswitch').hasClass('in')) {
                    setTimeout(function() {
                        showTabWarning();
                    }, 300);
                }
            });

            // Load jumlah pelanggaran saat halaman dimuat
            $.getJSON('<?php echo site_url().'/'.$url; ?>/get_violation_count/' + tesUserId, function(data) {
                if(data.data == 1) {
                    tabSwitchCount = data.total_violations;
                }
            });
        });
    });
</script>