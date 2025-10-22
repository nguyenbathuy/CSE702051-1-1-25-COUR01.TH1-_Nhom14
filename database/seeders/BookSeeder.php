<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Không Gia Đình',
                'isbn' => '9786041234567',
                'subject' => 'Tiểu thuyết cổ điển',
                'publication_date' => '2016-09-11',
                'cover_image' => 'images/books/849dd9eee54038dd6938431d4ba4074f.jpg',
            ],
            [
                'title' => 'Lịch sử văn minh trung hoa',
                'isbn' => '9786041234568',
                'subject' => 'Lịch sử',
                'publication_date' => '2015-06-22',
                'cover_image' => 'images/books/Ảnh-chụp-Màn-hình-2020-11-21-lúc-14.00.10.png',
            ],
            [
                'title' => 'Các triều đại việt nam',
                'isbn' => '9786041234569',
                'subject' => 'Lịch sử',
                'publication_date' => '2004-03-10',
                'cover_image' => 'images/books/cac-trieu-dai-viet-nam-1066463.jpg',
            ],
            [
                'title' => 'Chúa tể những chiếc nhẫn',
                'isbn' => '9786041234570',
                'subject' => 'Tiểu thuyết giả tưởng',
                'publication_date' => '2023-08-05',
                'cover_image' => 'images/books/Chua-te-nhung-chiec-nhan-dich-thuat-cong-chung-247.jpg',
            ],
            [
                'title' => 'Đắc nhân tâm',
                'isbn' => '9786041234571',
                'subject' => 'Phát triển bản thân',
                'publication_date' => '2015-11-12',
                'cover_image' => 'images/books/dac-nhan-tam_600x865.png',
            ],
            [
                'title' => 'Dế mèn phiêu lưu ký',
                'isbn' => '9786041234572',
                'subject' => 'Văn học thiếu nhi',
                'publication_date' => '2018-06-18',
                'cover_image' => 'images/books/de-men-phieu-luu-ky_ki.jpg',
            ],
            [
                'title' => 'Hoàng tử bé',
                'isbn' => '9786041234573',
                'subject' => 'Văn học thiếu nhi',
                'publication_date' => '2014-09-25',
                'cover_image' => 'images/books/kim-dong-1515.jpg',
            ],
            [
                'title' => 'Những người khốn khổ',
                'isbn' => '9786041234576',
                'subject' => 'Văn học cổ điển',
                'publication_date' => '2013-09-25',
                'cover_image' => 'images/books/nhung-nguoi-khon-kho-59284.jpg',
            ],
            [
                'title' => 'Viết lên hy vọng',
                'isbn' => '9786041234574',
                'subject' => 'Tự truyện',
                'publication_date' => '2013-09-25',
                'cover_image' => 'images/books/viet-hy-vong.png',
            ],
        ];

        foreach ($books as $bookData) {
            $book = Book::create($bookData);
            
            for ($i = 1; $i <= 5; $i++) {
                $book->bookItems()->create([
                    'barcode' => 'BC' . str_pad($book->id, 5, '0', STR_PAD_LEFT) . '-' . $i,
                    'format' => 'HARDCOVER',
                    'status' => 'AVAILABLE',
                    'rack_id' => rand(1, 10),
                ]);
            }
        }
    }
}