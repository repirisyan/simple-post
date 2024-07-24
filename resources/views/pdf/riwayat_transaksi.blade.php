<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
	<h1 class="text-center">Riwayat Transaksi</h1>
	<h2 class="text-center">{{$from_date}} s/d {{$until_date}}</h2>
	<hr>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>No</th>
				<th>Tanggal</th>
				<th>Invoice</th>
				<th>Total Pembelian</th>
			</tr>
		</thead>
		<tbody>
			@php
				$no = 1;
			@endphp
			@forelse ($data as $element)
				<tr>
					<td rowspan="2" class="text-center align-middle">{{$no++}}</td>
					<td>{{$element->created_at}}</td>
					<td>{{$element->invoice}}</td>
					<td>Rp. {{number_format($element->grand_total)}}</td>
				</tr>
				<tr>
					<td colspan="3">
						<table class="table table-borderless">
							<tbody>
								@foreach ($element->detail_transaction as $product)
									<tr>
										<td>{{$product->product->nama}}</td>
										<td>{{$product->qty}}</td>
										<td>Rp. {{number_format($product->buying_price)}}</td>
										<td>Rp. {{number_format($product->price)}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="4" class="text-center">
						Tidak ada data
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</body>
</html>