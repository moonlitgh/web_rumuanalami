$(document).ready(function(){
    // Load data provinsi saat halaman dimuat
    $.ajax({
        type: 'post',
        url: 'dataprovinsi.php',
        success: function(hasil_provinsi) {
            $("select[name=provinsi]").html(hasil_provinsi);
        }
    });

    // Event ketika provinsi dipilih
    $("select[name=provinsi]").on("change", function() {
        const id_provinsi_terpilih = $("option:selected", this).attr("id_provinsi");
        $.ajax({
            type: 'post',
            url: 'datadistrik.php',
            data: {
                id_provinsi: id_provinsi_terpilih
            },
            success: function(hasil_distrik) {
                $("select[name=kota_kabupaten]").html(hasil_distrik).prop('disabled', false);
                $("select[name=kurir]").prop('disabled', true);
                $("select[name=paket]").prop('disabled', true);
            }
        });
        $("input[name=provinsi_nama]").val($("option:selected", this).text());
    });

    // Event ketika kota/kabupaten dipilih
    $("select[name=kota_kabupaten]").on("change", function() {
        $.ajax({
            type: 'post',
            url: 'datakurir.php',
            success: function(hasil_kurir) {
                $("select[name=kurir]").html(hasil_kurir).prop('disabled', false);
                $("select[name=paket]").prop('disabled', true);
            }
        });
        $("input[name=distrik_nama]").val($("option:selected", this).attr("nama_distrik"));
    });

    // Event ketika kurir dipilih
    $("select[name=kurir]").on("change", function() {
        const ekspedisi_terpilih = $(this).val();
        const distrik_terpilih = $("option:selected", "select[name=kota_kabupaten]").attr("id_distrik");
        const total_berat = $("input[name=total_berat]").val();

        $.ajax({
            type: 'post',
            url: 'datapaket.php',
            data: {
                ekspedisi: ekspedisi_terpilih,
                distrik: distrik_terpilih,
                berat: total_berat
            },
            success: function(hasil_paket) {
                $("select[name=paket]").html(hasil_paket).prop('disabled', false);
            }
        });
        $("input[name=ekspedisi]").val(ekspedisi_terpilih);
    });

    // Event ketika paket dipilih
    $("select[name=paket]").on("change", function() {
        const selectedOption = $("option:selected", this);
        const paket = selectedOption.attr("paket");
        const ongkir = selectedOption.attr("ongkir");
        const etd = selectedOption.attr("etd");

        if(paket && ongkir && etd) {
            $("input[name=paket_nama]").val(paket);
            $("input[name=ongkir]").val(ongkir);
            $("input[name=estimasi]").val(etd);

            // Update tampilan ongkir
            $("#ongkir-amount").text("Rp " + formatNumber(ongkir));
            $("#ongkir-info").show();
            
            // Update total pembayaran
            updateTotalPayment();
        }
    });

    // Event ketika paket produk dipilih
    $("select[name=paket-order]").on("change", function() {
        updateTotalPayment();
    });

    // Fungsi untuk update total pembayaran
    function updateTotalPayment() {
        const hargaPaket = parseInt($("select[name=paket-order] option:selected").attr("data-harga")) || 0;
        const ongkir = parseInt($("input[name=ongkir]").val()) || 0;
        const total = hargaPaket + ongkir;

        $("#total-payment").text("Rp " + formatNumber(total));
        $("input[name=total_pembayaran]").val(total);
    }

    // Fungsi format number
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
}); 