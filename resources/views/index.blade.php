<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Главная</title>
    @livewireStyles
</head>
<body>
<div class="form-inline pull-right">
    <form method="POST" action="{{ route('get-site-map') }}">
        @csrf
        <br>
        <span>Вставьте ссылку</span>
        <input class="form-control" type="text" name="sitemap">
        @error('sitemap')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <br>
        <button type="submit" class="btn btn-success">Получить xml</button>
    </form>
</div>
<div class="form-inline pull-right">
    <form method="POST" action="{{ route('get-content') }}">
        @csrf
        <br>
        <span>Вставьте ключевые слова</span>
        <input class="form-control" type="text" name="content" placeholder="first|second|third|fourth">
        <br>
        @if($vendorsList)
            <select name="" id="vendorsList">
                @foreach($vendorsList as $vendor)
                    <option value="vendor">{{ $vendor->name }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" class="btn btn-success">Получить контент</button>
    </form>
</div>
<div class="col-md-12">
    <table class="table text-center">
        <tbody>
        <tr>
            <th>
                Название
            </th>
            <th>
                Артикул
            </th>
            <th>
                Цена
            </th>
        </tr>
        @foreach($contentList as $contentUnit)
            <tr>
                <td>{{ $contentUnit->Название }}</td>
                <td>{{ $contentUnit->Артикул }}</td>
                <td>{{ $contentUnit->Цена }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $contentList->links() }}
</div>
<livewire:counter>
@livewireScripts
</body>
</html>
