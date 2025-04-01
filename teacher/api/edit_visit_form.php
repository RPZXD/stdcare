<div class="flex flex-col items-center">
    <div class="w-full max-w-4xl">
        <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-lg text-center">
            <h5 class="text-lg font-bold">..:: เพิ่มข้อมูลการเยี่ยมบ้านนักเรียน ::..</h5>
            <hr class="my-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h6 class="text-sm font-medium">
                        <i class="fa fa-address-card"></i> เลขประจำตัวนักเรียน: <?= $data['Stu_id']; ?>
                    </h6>
                </div>
                <div>
                    <h6 class="text-sm font-medium">
                        <i class="fa fa-user"></i> ชื่อ-นามสกุล: <?= $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur']; ?>
                    </h6>
                </div>
                <div>
                    <h6 class="text-sm font-medium">
                        <i class="fa fa-address-card"></i> ชั้น: <?= $data['Stu_major'] . "/" . $data['Stu_room']; ?>
                    </h6>
                </div>
                <div>
                    <h6 class="text-sm font-medium">
                        <i class="fa fa-home"></i> ที่อยู่: <?= $data['Stu_addr']; ?>
                    </h6>
                </div>
                <div>
                    <h6 class="text-sm font-medium">
                        <i class="fa fa-phone"></i> เบอร์โทรศัพท์: <?= $data['Stu_phone']; ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full max-w-4xl mt-6">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-6 rounded-lg">
            <form method="post" enctype="multipart/form-data">
                <p class="text-sm font-medium mb-4">กรอกข้อมูลในแบบฟอร์มให้ตรงตามความเป็นจริง</p>

                <?php
                $questions = [
                    "1. บ้านที่อยู่อาศัย" => ["บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น"],
                    "2. ระยะทางระหว่างบ้านกับโรงเรียน" => ["1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กิโลเมตรขึ้นไป"],
                    "3. การเดินทางไปโรงเรียนของนักเรียน" => ["เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่งรถโดยสาร", "อื่นๆ"],
                    "4. สภาพแวดล้อมของบ้าน" => ["ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง"],
                    "5. อาชีพของผู้ปกครอง" => ["เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ"],
                    "6. สถานที่ทำงานของบิดามารดา" => ["ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ"],
                    "7. สถานภาพของบิดามารดา" => ["บิดามารดาอยู่ด้วยกัน", "บิดามารดาหย่าร้างกัน", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "บิดาและมารดาถึงแก่กรรม"],
                    "8. วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน" => ["เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ"],
                    "9. โรคประจำตัวของนักเรียน" => ["ไม่มี", "มี"],
                    "10. ความสัมพันธ์ของสมาชิกในครอบครัว" => ["อบอุ่น", "เฉยๆ", "ห่างเหิน"],
                    "11. หน้าที่รับผิดชอบภายในบ้าน" => ["มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี"],
                    "12. สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
                    "13. รายได้กับการใช้จ่ายในครอบครัว" => ["เพียงพอ", "ไม่เพียงพอในบางครั้ง", "ขัดสน"],
                    "14. ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น" => ["เพื่อนรุ่นเดียวกัน", "เพื่อนรุ่นน้อง", "เพื่อนรุ่นพี่", "เพื่อนทุกรุ่น"],
                    "15. ความต้องการของผู้ปกครอง เมื่อนักเรียนจบชั้นสูงสุดของโรงเรียน" => ["ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ"],
                    "16. เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร" => ["พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ"],
                    "17. ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
                    "18. ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน" => ["พอใจ", "เฉยๆ", "ไม่พอใจ"],
                ];

                $i = 1;
                foreach ($questions as $question => $options) {
                    echo '<div class="mb-4">';
                    echo '<h5 class="text-sm font-bold mb-2">' . $question . '</h5>';
                    echo '<div class="flex flex-wrap gap-4">';
                    foreach ($options as $index => $option) {
                        $radioId = 'vh' . $i . '-' . $index;
                        $isChecked = isset($data['vh' . $i]) && $data['vh' . $i] == ($index + 1) ? 'checked' : ''; // Pre-fill the radio button
                        echo '<div class="flex items-center space-x-2">';
                        echo '<input type="radio" id="' . $radioId . '" name="vh' . $i . '" value="' . ($index + 1) . '" ' . $isChecked . ' required class="form-radio text-blue-500">';
                        echo '<label for="' . $radioId . '" class="text-sm">' . $option . '</label>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                    $i++;
                }
                ?>

                <div class="mb-4">
                    <h5 class="text-sm font-bold mb-2">19. รูปถ่ายการเยี่ยมบ้านนักเรียน (ได้สูงสุด 5 รูปภาพ)</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        $images = [
                            ["id" => "image1", "label" => "รูปภาพที่ 1", "description" => "* ภาพตัวบ้านนักเรียน (ให้เห็นทั้งหลัง)"],
                            ["id" => "image2", "label" => "รูปภาพที่ 2", "description" => "* ภาพภายในบ้านนักเรียน"],
                            ["id" => "image3", "label" => "รูปภาพที่ 3", "description" => "* ภาพขณะครูเยี่ยมบ้านกับนักเรียนและผู้ปกครอง"],
                            ["id" => "image4", "label" => "รูปภาพที่ 4", "description" => "=> ภาพเพิ่มเติม"],
                            ["id" => "image5", "label" => "รูปภาพที่ 5", "description" => "=> ภาพเพิ่มเติม"],
                        ];

                        foreach ($images as $image) {
                            $imageValue = isset($data[$image['id']]) ? $data[$image['id']] : '';
                            echo '
                            <div class="text-center">
                                <label for="' . $image['id'] . '" class="block text-sm font-medium">' . $image['label'] . ': <span class="text-red-500">' . $image['description'] . '</span></label>
                                <input type="file" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" name="' . $image['id'] . '" id="' . $image['id'] . '" accept="image/*">
                                <p class="mt-2">' . ($imageValue ? '<img src="' . $imageValue . '" alt="Uploaded Image" class="w-24 h-24 object-cover rounded-lg">' : 'No image uploaded') . '</p>
                            </div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="text-sm font-bold mb-2">20. ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ</h5>
                    <textarea name="vh20" id="vh20" cols="30" rows="5" class="w-full p-2 border border-gray-300 rounded-lg"><?= isset($data['vh20']) ? htmlspecialchars($data['vh20']) : ''; ?></textarea>
                </div>

                <div class="mt-6">
                    <input type="hidden" name="stuId" value="<?= $data['Stu_id']; ?>">
                    <input type="hidden" name="term" value="<?= $data['Term']; ?>">
                    <input type="hidden" name="pee" value="<?= $data['Pee']; ?>">
                    <button type="submit" name="btn_submit" class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>