<?php 
session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));

// Image utility functions
function convertImageToJpeg($sourcePath, $targetPath, $quality = 85) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $sourceImage = null;
    switch ($imageInfo['mime']) {
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($sourcePath);
            break;
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    // Enable transparency for PNG/GIF conversion
    if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/gif') {
        $background = imagecreatetruecolor(imagesx($sourceImage), imagesy($sourceImage));
        $white = imagecolorallocate($background, 255, 255, 255);
        imagefill($background, 0, 0, $white);
        imagecopy($background, $sourceImage, 0, 0, 0, 0, imagesx($sourceImage), imagesy($sourceImage));
        imagedestroy($sourceImage);
        $sourceImage = $background;
    }
    
    $result = imagejpeg($sourceImage, $targetPath, $quality);
    imagedestroy($sourceImage);
    return $result;
}

function resizeImage($sourcePath, $targetPath, $maxWidth = 800, $maxHeight = 600, $quality = 85) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
    if ($ratio >= 1) {
        // Image is smaller than max dimensions, just convert format if needed
        return convertImageToJpeg($sourcePath, $targetPath, $quality);
    }
    
    $newWidth = round($sourceWidth * $ratio);
    $newHeight = round($sourceHeight * $ratio);
    
    $sourceImage = null;
    switch ($imageInfo['mime']) {
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($sourcePath);
            break;
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    $targetImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Handle transparency for PNG/GIF
    if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/gif') {
        $white = imagecolorallocate($targetImage, 255, 255, 255);
        imagefill($targetImage, 0, 0, $white);
    }
    
    imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, 
                      $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
    $result = imagejpeg($targetImage, $targetPath, $quality);
    
    imagedestroy($sourceImage);
    imagedestroy($targetImage);
    
    return $result;
}

// Function to generate visit form
function generateVisitForm($data, $isEdit = false, $term = null, $pee = null) {
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
    ];    $images = [
        ["id" => "image1", "label" => "รูปภาพที่ 1", "description" => "* ภาพตัวบ้านนักเรียน (ให้เห็นทั้งหลัง)", "required" => true],
        ["id" => "image2", "label" => "รูปภาพที่ 2", "description" => "* ภาพภายในบ้านนักเรียน", "required" => true],
        ["id" => "image3", "label" => "รูปภาพที่ 3", "description" => "* ภาพขณะครูเยี่ยมบ้านกับนักเรียนและผู้ปกครอง", "required" => true],
        ["id" => "image4", "label" => "รูปภาพที่ 4", "description" => "=> ภาพเพิ่มเติม", "required" => false],
        ["id" => "image5", "label" => "รูปภาพที่ 5", "description" => "=> ภาพเพิ่มเติม", "required" => false],
    ];

    $formId = $isEdit ? 'editVisitForm' : 'addVisitForm';
    $actionText = $isEdit ? 'แก้ไขข้อมูลการเยี่ยมบ้านนักเรียน' : 'บันทึกข้อมูลการเยี่ยมบ้านนักเรียน';
    
    ob_start();
    ?>
    <div class="flex flex-col items-center space-y-6">
        <!-- Student Info Card -->
        <div class="w-full">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-center mb-4">
                    <h5 class="text-2xl font-bold text-white drop-shadow-md">
                        ✨ <?= $actionText ?> ✨
                    </h5>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-300">🆔</span>
                            <span class="font-medium">เลขประจำตัว:</span>
                            <span class="font-bold"><?= $data['Stu_id']; ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-300">👤</span>
                            <span class="font-medium">ชื่อ-สกุล:</span>
                            <span class="font-bold"><?= $data['Stu_pre'] . $data['Stu_name'] . " " . $data['Stu_sur']; ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-300">🏫</span>
                            <span class="font-medium">ชั้น:</span>
                            <span class="font-bold"><?= $data['Stu_major'] . "/" . $data['Stu_room']; ?></span>
                        </div>
                        <div class="flex items-center space-x-2 md:col-span-2">
                            <span class="text-yellow-300">🏠</span>
                            <span class="font-medium">ที่อยู่:</span>
                            <span class="font-bold"><?= $data['Stu_addr']; ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-yellow-300">📞</span>
                            <span class="font-medium">เบอร์โทรศัพท์:</span>
                            <span class="font-bold"><?= $data['Stu_phone']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 p-6 rounded-xl shadow-lg">
                <form method="post" id="<?= $formId ?>" enctype="multipart/form-data" class="space-y-6">
                    
                    <!-- Instructions -->
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">📝</span>
                            <p class="text-amber-800 font-medium">
                                กรอกข้อมูลในแบบฟอร์มให้<?= $isEdit ? 'ตรงตามความเป็นจริง' : 'ครบถ้วนและถูกต้อง' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Questions Section -->
                    <div class="space-y-6">
                        <?php
                        $i = 1;
                        foreach ($questions as $question => $options) {
                            echo '<div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">';
                            echo '<h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">';
                            echo '<span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">' . $i . '</span>';
                            echo substr($question, 2) . '</h5>'; // Remove number from question text
                            echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">';
                            foreach ($options as $index => $option) {
                                $radioId = 'vh' . $i . '-' . $index;
                                $isChecked = $isEdit && isset($data['vh' . $i]) && $data['vh' . $i] == ($index + 1) ? 'checked' : '';
                                echo '<label for="' . $radioId . '" class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 group">';
                                echo '<input type="radio" id="' . $radioId . '" name="vh' . $i . '" value="' . ($index + 1) . '" ' . $isChecked . ' required class="form-radio text-blue-500 mr-3 group-hover:ring-2 group-hover:ring-blue-200">';
                                echo '<span class="text-gray-700 group-hover:text-blue-700 font-medium">' . $option . '</span>';
                                echo '</label>';
                            }
                            echo '</div></div>';
                            $i++;
                        }
                        ?>
                    </div>                    <!-- Image Upload Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center mb-6">
                            <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">19</span>
                            <h5 class="text-lg font-bold text-gray-800">รูปถ่ายการเยี่ยมบ้านนักเรียน</h5>
                            <span class="ml-2 text-sm text-gray-500">(ได้สูงสุด 5 รูปภาพ)</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php
                            foreach ($images as $index => $image) {
                                $isRequired = $image['required'] ? 'required' : '';
                                $requiredText = $image['required'] ? '<span class="text-red-500">*</span>' : '';
                                
                                echo '<div class="group">';
                                echo '<div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-300">';
                                
                                // Label
                                echo '<label for="' . $image['id'] . '" class="block cursor-pointer">';
                                echo '<div class="mb-3">';
                                echo '<h6 class="font-bold text-gray-700 mb-1">' . $image['label'] . ' ' . $requiredText . '</h6>';
                                echo '<p class="text-sm text-gray-600">' . $image['description'] . '</p>';
                                echo '</div>';
                                
                                // Upload Area
                                echo '<div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-400 transition-colors duration-200" id="upload-area-' . $image['id'] . '">';
                                echo '<div class="upload-content">';
                                echo '<svg class="mx-auto h-12 w-12 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">';
                                echo '<path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />';
                                echo '</svg>';
                                echo '<p class="text-sm text-gray-600 mb-2"><span class="font-semibold">คลิกเพื่ออัพโหลด</span></p>';
                                echo '<p class="text-xs text-gray-500">รองรับ JPG, PNG, GIF, WebP, BMP (ขนาดไม่เกิน 5MB)</p>';
                                echo '</div>';
                                echo '</div>';
                                
                                // Hidden file input - ลบ onchange attribute ออก
                                echo '<input type="file" class="hidden" name="' . $image['id'] . '" id="' . $image['id'] . '" accept="image/*" ' . $isRequired . '>';
                                // เพิ่ม hidden input สำหรับ remove flag
                                echo '<input type="hidden" name="remove_' . $image['id'] . '" id="remove_' . $image['id'] . '" value="0">';
                                echo '</label>';
                                
                                // Preview area
                                echo '<div class="mt-4 preview-container" id="preview-' . $image['id'] . '">';
                                if ($isEdit && isset($data['picture' . substr($image['id'], -1)]) && $data['picture' . substr($image['id'], -1)]) {
                                    $imagePath = "../teacher/uploads/visithome" . ($data['Pee'] - 543) . "/" . $data['picture' . substr($image['id'], -1)];
                                    echo '<div class="relative group">';
                                    echo '<img src="' . $imagePath . '" alt="Uploaded Image" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 shadow-sm">';
                                    echo '<div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">';
                                    // ปรับปุ่มลบให้เรียก removeImage พร้อมตั้ง remove flag
                                    echo '<button type="button" onclick="removeImage(\'' . $image['id'] . '\', true)" class="bg-red-500 text-white px-3 py-1 rounded-full text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">';
                                    echo '<i class="fas fa-trash mr-1"></i>ลบ';
                                    echo '</button>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '<p class="text-xs text-green-600 mt-2 font-medium"><i class="fas fa-check-circle mr-1"></i>อัพโหลดแล้ว</p>';
                                } else {
                                    echo '<p class="text-xs text-gray-400 mt-2" id="status-' . $image['id'] . '">ยังไม่ได้อัพโหลดรูปภาพ</p>';
                                }
                                echo '</div>';
                                
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        
                        <!-- Upload Instructions -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <span class="text-blue-500 text-xl mr-3">💡</span>
                                <div class="text-sm text-blue-700">
                                    <p class="font-semibold mb-2">คำแนะนำการอัพโหลดรูปภาพ:</p>
                                    <ul class="space-y-1 text-xs">
                                        <li>• รูปภาพจะถูกปรับขนาดอัตโนมัติเป็น 800x600 พิกเซล</li>
                                        <li>• รูปภาพทุกรูปแบบจะถูกแปลงเป็น JPG เพื่อประหยัดพื้นที่</li>
                                        <li>• ขนาดไฟล์ไม่ควรเกิน 5MB</li>
                                        <li>• รูปภาพที่มี <span class="text-red-500">*</span> เป็นรูปภาพที่จำเป็นต้องอัพโหลด</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Problems/Obstacles Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center mb-4">
                            <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">20</span>
                            <h5 class="text-lg font-bold text-gray-800">ปัญหา/อุปสรรค และความต้องการความช่วยเหลือ</h5>
                        </div>
                        <textarea name="vh20" id="vh20" rows="6" 
                                  class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                  placeholder="กรุณาระบุปัญหา อุปสรรค และความต้องการความช่วยเหลือ..."><?= $isEdit && isset($data['vh20']) ? htmlspecialchars($data['vh20']) : ''; ?></textarea>
                        <p class="text-xs text-gray-500 mt-2">สามารถเว้นว่างได้หากไม่มีปัญหาหรืออุปสรรคใดๆ</p>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="stuId" value="<?= $data['Stu_id']; ?>">
                    <input type="hidden" name="term" value="<?= $isEdit ? $data['Term'] : $term; ?>">
                    <input type="hidden" name="pee" value="<?= $isEdit ? $data['Pee'] : $pee; ?>">
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .upload-area {
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background-color: #f8fafc;
            border-color: #3b82f6;
        }
        .upload-area.dragover {
            background-color: #eff6ff;
            border-color: #2563eb;
            transform: scale(1.02);
        }
        .form-radio:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .preview-image {
            transition: all 0.3s ease;
        }
        .preview-image:hover {
            transform: scale(1.05);
        }
    </style>
    
    <script>
        // Image upload handling
        function handleImageUpload(input, imageId) {
            const file = input.files[0];
            if (!file) return;
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('ข้อผิดพลาด', 'ขนาดไฟล์ใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB', 'error');
                input.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
            if (!validTypes.includes(file.type)) {
                Swal.fire('ข้อผิดพลาด', 'รูปแบบไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์รูปภาพ', 'error');
                input.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById('preview-' + imageId);
                const statusElement = document.getElementById('status-' + imageId);
                
                previewContainer.innerHTML = `
                    <div class="relative group">
                        <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 shadow-sm preview-image">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                            <button type="button" onclick="removeImage('${imageId}')" class="bg-red-500 text-white px-3 py-1 rounded-full text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                <i class="fas fa-trash mr-1"></i>ลบ
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 mt-2 font-medium"><i class="fas fa-check-circle mr-1"></i>เลือกแล้ว - ${file.name}</p>
                `;
            };
            reader.readAsDataURL(file);
            
            // เมื่ออัพโหลดใหม่ ให้ clear remove flag
            const removeInput = document.getElementById('remove_' + imageId);
            if (removeInput) removeInput.value = "0";
        }
        
        function removeImage(imageId, isEdit = false) {
            const input = document.getElementById(imageId);
            const previewContainer = document.getElementById('preview-' + imageId);
            input.value = '';
            previewContainer.innerHTML = `<p class="text-xs text-gray-400 mt-2" id="status-${imageId}">ยังไม่ได้อัพโหลดรูปภาพ</p>`;
            // set remove flag ถ้าเป็นโหมดแก้ไข
            if (isEdit) {
                const removeInput = document.getElementById('remove_' + imageId);
                if (removeInput) removeInput.value = "1";
            }
        }
        
        // Drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            const uploadAreas = document.querySelectorAll('.upload-area');
            
            uploadAreas.forEach(area => {
                const imageId = area.id.replace('upload-area-', '');
                const input = document.getElementById(imageId);
                
                area.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    area.classList.add('dragover');
                });
                
                area.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    area.classList.remove('dragover');
                });
                
                area.addEventListener('drop', function(e) {
                    e.preventDefault();
                    area.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleImageUpload(input, imageId);
                    }
                });
                
                area.addEventListener('click', function() {
                    input.click();
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}

// Handle AJAX requests for forms
if (isset($_GET['action'])) {
    header('Content-Type: text/html; charset=utf-8');
    
    if ($_GET['action'] === 'get_edit_form') {
        $term = $_GET['term'];
        $pee = $_GET['pee'];
        $stuId = $_GET['stuId'];
        
        try {
            // Try different possible table names
            $possibleTables = ['visithome', 'visit_home', 'home_visit'];
            $data = null;
            
        foreach ($possibleTables as $tableName) {
            try {
                $sql = "SELECT v.*, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_addr, s.Stu_phone 
                        FROM {$tableName} v 
                        JOIN student s ON v.Stu_id = s.Stu_id 
                        WHERE v.Term = ? AND v.Pee = ? AND v.Stu_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$term, $pee, $stuId]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data) break; // แก้ไขจาก "if $data) break;"
            } catch (PDOException $e) {
                continue; // Try next table name
            }
        }
            
            if ($data) {
                echo generateVisitForm($data, true);
            } else {
                echo '';
            }
        } catch (Exception $e) {
            echo '';
        }
        exit;
    }
    
    if ($_GET['action'] === 'get_add_form') {
        $term = $_GET['term'];
        $pee = $_GET['pee'];
        $stuId = $_GET['stuId'];
        
        try {
            // Fetch student data only
            $sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_addr, Stu_phone 
                    FROM student WHERE Stu_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$stuId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                echo generateVisitForm($data, false, $term, $pee);
            } else {
                echo '';
            }
        } catch (Exception $e) {
            echo '';
        }
        exit;
    }
}

require_once('header.php');
?>

<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"></h1>
                    </div>
                </div>
            </div>
        </div>        <section class="content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <!-- Main Header Card -->
                    <div class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 p-6 rounded-xl shadow-lg text-white mb-6">
                        <div class="text-center">
                            <div class="flex justify-center items-center mb-4">
                                <img src="../dist/img/logo-phicha.png" alt="Phichai Logo" class="w-20 h-20 mr-4 drop-shadow-lg">
                                <div>
                                    <h1 class="text-3xl font-bold drop-shadow-md">
                                        🏠 แบบฟอร์มบันทึกการเยี่ยมบ้านนักเรียน
                                    </h1>
                                    <p class="text-xl mt-2 text-blue-100">
                                        ระดับชั้นมัธยมศึกษาปีที่ <?= $class."/".$room; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap justify-center gap-4 mt-6">
                            <button type="button" 
                                    onclick="window.location.href='visithome_report_class.php'"
                                    class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-lg shadow-md hover:bg-white/30 transition-all duration-300 flex items-center space-x-2 border border-white/30">
                                <span class="text-xl">📊</span>
                                <span class="font-medium">รายงานสถิติข้อมูลการเยี่ยมบ้าน</span>
                            </button>
                            <button onclick="printPage()" 
                                    class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-lg shadow-md hover:bg-white/30 transition-all duration-300 flex items-center space-x-2 border border-white/30" 
                                    id="printButton">
                                <span class="text-xl">🖨️</span>
                                <span class="font-medium">พิมพ์รายงาน</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Data Table Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <span class="mr-3">📋</span>
                                รายชื่อนักเรียนและสถานะการเยี่ยมบ้าน
                            </h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table id="record_table" class="w-full border-collapse bg-white rounded-lg overflow-hidden shadow-sm">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold">เลขที่</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold">เลขประจำตัว</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold">ชื่อ-นามสกุล</th>
                                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold">
                                                <div class="flex flex-col items-center">
                                                    <span>เยี่ยมบ้านครั้งที่ 1</span>
                                                    <span class="text-xs text-blue-200">(100%)</span>
                                                </div>
                                            </th>
                                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold">
                                                <div class="flex flex-col items-center">
                                                    <span>เยี่ยมบ้านครั้งที่ 2</span>
                                                    <span class="text-xs text-blue-200">(ออนไลน์ 75%, บ้าน 25%)</span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Instructions Card -->
                    <div class="mt-6 bg-gradient-to-br from-red-50 to-orange-50 border border-red-200 p-6 rounded-xl shadow-lg">
                        <div class="flex items-center mb-4">
                            <span class="text-3xl mr-4">⚠️</span>
                            <h4 class="text-2xl font-bold text-red-600">คำแนะนำการใช้งาน</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                            <div class="space-y-3">
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-blue-500 text-xl">📖</span>
                                    <span class="text-sm">เมื่อต้องการดูสรุปสถิติข้อมูลการเยี่ยมบ้านนักเรียนให้คลิกที่ "รายงานสถิติข้อมูลการเยี่ยมบ้าน"</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-green-500 text-xl">💾</span>
                                    <span class="text-sm">เมื่อต้องการบันทึกข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "บันทึก"</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-yellow-500 text-xl">✏️</span>
                                    <span class="text-sm">เมื่อต้องการแก้ไขข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "แก้ไข"</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-purple-500 text-xl">👁️</span>
                                    <span class="text-sm">เมื่อต้องการดูรายละเอียดข้อมูลการเยี่ยมบ้านของนักเรียนให้คลิกที่ "ดู"</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-indigo-500 text-xl">🏠</span>
                                    <span class="text-sm">เยี่ยมบ้านครั้งที่ 1 ให้ดำเนินการเยี่ยมบ้าน และกรอกข้อมูลนักเรียนให้ครบทุกคน</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-orange-500 text-xl">🎯</span>
                                    <span class="text-sm">เยี่ยมบ้านครั้งที่ 2 ให้ดำเนินการเยี่ยมบ้าน เฉพาะนักเรียนกลุ่มเสี่ยง หรือกลุ่มที่มีความต้องการพิเศษ ร้อยละ 25</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-teal-500 text-xl">📝</span>
                                    <span class="text-sm">โดยกรอกข้อมูลของนักเรียนเฉพาะรายบุคคลเท่านั้น</span>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <span class="text-pink-500 text-xl">📷</span>
                                    <span class="text-sm">อัพโหลดรูปภาพการเยี่ยมบ้านอย่างน้อย 3 รูป และสามารถอัพโหลดได้สูงสุด 5 รูป</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <?php require_once('../footer.php');?>
</div>

<!-- Modal for Editing Visit -->
<div class="modal fade" id="editVisitModal" tabindex="-1" aria-labelledby="editVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-t-xl">
                <h5 class="modal-title text-xl font-bold flex items-center" id="editVisitModalLabel">
                    <span class="text-2xl mr-3">✏️</span>
                    แก้ไขข้อมูลการเยี่ยมบ้าน
                </h5>
                <button type="button" class="close text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-2xl">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="editVisitContent" class="max-h-[70vh] overflow-y-auto"></div>
                <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200">
                    <button type="button" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition-all duration-200 mr-3 flex items-center space-x-2" 
                            data-dismiss="modal">
                        <span>❌</span>
                        <span>ปิด</span>
                    </button>
                    <button type="button" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center space-x-2" 
                            id="saveEditVisit">
                        <span>💾</span>
                        <span>บันทึกการเปลี่ยนแปลง</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding Visit -->
<div class="modal fade" id="addVisitModal" tabindex="-1" aria-labelledby="addVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-green-400 to-blue-500 text-white rounded-t-xl">
                <h5 class="modal-title text-xl font-bold flex items-center" id="addVisitModalLabel">
                    <span class="text-2xl mr-3">➕</span>
                    เพิ่มข้อมูลการเยี่ยมบ้าน
                </h5>
                <button type="button" class="close text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-2xl">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="addVisitContent" class="max-h-[70vh] overflow-y-auto"></div>
                <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200">
                    <button type="button" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 transition-all duration-200 mr-3 flex items-center space-x-2" 
                            data-dismiss="modal">
                        <span>❌</span>
                        <span>ปิด</span>
                    </button>
                    <button type="button" 
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-md hover:from-green-600 hover:to-green-700 transition-all duration-200 flex items-center space-x-2" 
                            id="saveAddVisit">
                        <span>💾</span>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('script.php'); ?>

<script>
$(document).ready(function() {
    // Function to handle printing
    window.printPage = function() {
        let elementsToHide = $('#addButton, #showBehavior, #printButton, #filter, #reset, #addTraining, #footer, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, .btn-warning, .btn-primary');
        elementsToHide.hide();
        $('thead').css('display', 'table-header-group');
        $('#record_table_wrapper .dt-buttons').hide();
        setTimeout(() => {
            window.print();
            elementsToHide.show();
            $('#record_table_wrapper .dt-buttons').show();
        }, 100);
    };

    // Enhanced image validation function
    function validateImageFile(file) {
        // File size validation (5MB)
        if (file.size > 5 * 1024 * 1024) {
            return { valid: false, message: 'ขนาดไฟล์ใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB' };
        }
        
        // File type validation
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        if (!validTypes.includes(file.type)) {
            return { valid: false, message: 'รูปแบบไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์รูปภาพ (JPEG, PNG, GIF, WebP, BMP)' };
        }
        
        return { valid: true };
    }

    // Enhanced form validation function
    function validateForm(formId) {
        const form = document.getElementById(formId);
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        let firstInvalidField = null;

        // Reset previous error states
        requiredFields.forEach(field => {
            field.classList.remove('border-red-500');
            const errorMsg = field.parentNode.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        });

        // Validate each required field
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                
                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-red-500 text-xs mt-1';
                errorDiv.textContent = 'กรุณากรอกข้อมูลให้ครบถ้วน';
                field.parentNode.appendChild(errorDiv);
                
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });

        // Validate required images (เฉพาะกรณี add หรือ edit ที่ไม่มีรูปเดิม หรือกดลบรูปเดิม)
        const requiredImages = form.querySelectorAll('input[type="file"][required]');
        requiredImages.forEach(imageInput => {
            const imageId = imageInput.id;
            const removeInput = form.querySelector(`#remove_${imageId}`);
            let hasOldImage = false;
            // ตรวจสอบว่ามีรูปเดิมหรือไม่ (ดูจาก hidden input ที่มีชื่อ pictureX)
            // หรือดูจาก previewContainer มี <img> และ removeInput ไม่ถูกลบ
            const previewContainer = document.getElementById('preview-' + imageId);
            if (previewContainer && previewContainer.querySelector('img')) {
                if (removeInput && removeInput.value === "1") {
                    hasOldImage = false;
                } else {
                    hasOldImage = true;
                }
            }
            // ถ้าไม่มีไฟล์ใหม่ และ (ไม่มีรูปเดิม)
            if (
                !imageInput.files.length &&
                !hasOldImage
            ) {
                isValid = false;
                const uploadArea = imageInput.closest('.upload-area');
                if (uploadArea) {
                    uploadArea.classList.add('border-red-500');
                    // ลบ error ซ้ำ
                    let errorDiv = uploadArea.querySelector('.error-message');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-red-500 text-xs mt-2 text-center';
                        errorDiv.textContent = 'กรุณาเลือกรูปภาพ';
                        uploadArea.appendChild(errorDiv);
                    }
                }
                if (!firstInvalidField) {
                    firstInvalidField = imageInput;
                }
            }
        });

        // Scroll to first invalid field
        if (firstInvalidField) {
            // ป้องกัน scroll ซ้ำซ้อนใน modal
            setTimeout(() => {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        return isValid;
    }

    // Optimized loadTable function with error handling
    async function loadTable() {
        try {
            const response = await $.ajax({
                url: 'api/fetch_visit_class.php',
                method: 'GET',
                dataType: 'json',
                data: { class: <?= $class ?>, room: <?= $room ?>, pee: <?= $pee ?> }
            });

            if (!response.success) {
                console.warn('API returned error:', response.message);
                // Still initialize table with empty data
                initializeEmptyTable();
                return;
            }

            const table = $('#record_table').DataTable({
                destroy: true,
                pageLength: 50,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: [0, 1, 3, 4], className: 'text-center' },
                    { targets: 2, className: 'text-left text-semibold' }
                ],
                autoWidth: false,
                info: true,
                lengthChange: true,
                ordering: true,
                responsive: true,
                paging: true,
                searching: true
            });

            table.clear();

            if (response.data.length === 0) {
                table.row.add(['<td colspan="5" class="text-center">ไม่พบข้อมูล</td>']);
            } else {
                response.data.forEach((item, index) => {
                    const visit1Status = item.visit_status1 === 1
                        ? '<span class="text-success">✅</span> <button class="btn btn-warning btn-sm" onclick="editVisit(1, \'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn btn-primary btn-sm" onclick="addVisit(1, \'' + item.Stu_id + '\')"><i class="fas fa-save"></i> บันทึก</button>';
                    
                    const visit2Status = item.visit_status2 === 1
                        ? '<span class="text-success">✅</span> <button class="btn btn-warning btn-sm" onclick="editVisit(2, \'' + item.Stu_id + '\')"><i class="fas fa-edit"></i> แก้ไข</button>'
                        : '<span class="text-danger">❌</span> <button class="btn btn-primary btn-sm" onclick="addVisit(2, \'' + item.Stu_id + '\')"><i class="fas fa-save"></i> บันทึก</button>';

                    table.row.add([
                        index + 1,
                        item.Stu_id,
                        item.FullName,
                        visit1Status,
                        visit2Status
                    ]);
                });
            }
            table.draw();
        } catch (error) {
            console.error('Error loading table:', error);
            initializeEmptyTable();
        }
    }

    function initializeEmptyTable() {
        const table = $('#record_table').DataTable({
            destroy: true,
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [0, 1, 3, 4], className: 'text-center' },
                { targets: 2, className: 'text-left text-semibold' }
            ],
            autoWidth: false,
            info: true,
            lengthChange: true,
            ordering: true,
            responsive: true,
            paging: true,
            searching: true
        });
        
        table.clear();
        table.row.add(['<td colspan="5" class="text-center">ไม่สามารถโหลดข้อมูลได้ กรุณาตรวจสอบการเชื่อมต่อฐานข้อมูล</td>']);
        table.draw();
    }

    // Enhanced editVisit function
    window.editVisit = function(term, stuId) {
        // Show loading
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '?action=get_edit_form',
            method: 'GET',
            data: { term: term, pee: <?= $pee ?>, stuId: stuId },
            dataType: 'html',
            success: function(response) {
                Swal.close();
                if (response.trim() === '') {
                    Swal.fire('ข้อผิดพลาด', 'ไม่พบข้อมูลการเยี่ยมบ้าน หรือยังไม่ได้บันทึกข้อมูล', 'warning');
                    return;
                }
                $('#editVisitContent').html(response);
                $('#editVisitModal').modal('show');

                // Initialize drag and drop for edit form
                initializeDragAndDrop('editVisitForm');
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้: ' + error, 'error');
            }
        });
    };

    // Enhanced addVisit function
    window.addVisit = function(term, stuId) {
        // Show loading
        Swal.fire({
            title: 'กำลังโหลดฟอร์ม...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '?action=get_add_form',
            method: 'GET',
            data: { term: term, pee: <?= $pee ?>, stuId: stuId },
            dataType: 'html',
            success: function(response) {
                Swal.close();
                if (response.trim() === '') {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้', 'error');
                    return;
                }
                $('#addVisitContent').html(response);
                $('#addVisitModal').modal('show');

                // Initialize drag and drop for add form
                initializeDragAndDrop('addVisitForm');
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดฟอร์มได้: ' + error, 'error');
            }
        });
    };

    // Initialize drag and drop functionality - ปรับปรุงให้ไม่ซ้ำ
    function initializeDragAndDrop(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const uploadAreas = form.querySelectorAll('.upload-area');
        
        uploadAreas.forEach(area => {
            const imageId = area.id.replace('upload-area-', '');
            const input = form.querySelector('#' + imageId);
            
            if (!input) return;

            // ลบ event listeners เก่าทั้งหมดก่อน
            const newArea = area.cloneNode(true);
            area.parentNode.replaceChild(newArea, area);
            
            const newInput = form.querySelector('#' + imageId);
            
            // เพิ่ม event listeners ใหม่
            newArea.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                newInput.click();
            });
            
            newInput.addEventListener('change', function(e) {
                e.stopPropagation();
                if (this.files.length > 0) {
                    handleImageUpload(this, imageId);
                }
            });
            
            // Drag and drop events
            newArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            newArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            newArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    newInput.files = dt.files;
                    handleImageUpload(newInput, imageId);
                }
            });
        });
    }

    // ปรับปรุง handleImageUpload ให้ป้องกันการทำงานซ้ำ
    window.handleImageUpload = function(input, imageId) {
        const file = input.files[0];
        if (!file) return;
        
        // ป้องกันการทำงานซ้ำ
        if (input.dataset.processing === 'true') {
            return;
        }
        input.dataset.processing = 'true';
        
        // Validate file
        const validation = validateImageFile(file);
        if (!validation.valid) {
            Swal.fire('ข้อผิดพลาด', validation.message, 'error');
            input.value = '';
            input.dataset.processing = 'false';
            return;
        }
        
        // Remove error states
        const uploadArea = input.closest('.upload-area');
        if (uploadArea) {
            uploadArea.classList.remove('border-red-500');
            const errorMsg = uploadArea.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('preview-' + imageId);
            
            previewContainer.innerHTML = `
                <div class="relative group">
                    <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 shadow-sm preview-image">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                        <button type="button" onclick="removeImage('${imageId}')" class="bg-red-500 text-white px-3 py-1 rounded-full text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                            <i class="fas fa-trash mr-1"></i>ลบ
                        </button>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <p class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>เลือกแล้ว</p>
                    <p class="text-xs text-gray-500">${file.name}</p>
                    <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            `;
            
            // Reset processing flag
            input.dataset.processing = 'false';
        };
        reader.readAsDataURL(file);
    };

    // Enhanced removeImage function
    window.removeImage = function(imageId) {
        const input = document.getElementById(imageId);
        const previewContainer = document.getElementById('preview-' + imageId);
        
        input.value = '';
        previewContainer.innerHTML = `<p class="text-xs text-gray-400 mt-2" id="status-${imageId}">ยังไม่ได้อัพโหลดรูปภาพ</p>`;
        
        // Remove error states
        const uploadArea = input.closest('.upload-area');
        if (uploadArea) {
            uploadArea.classList.remove('border-red-500');
            const errorMsg = uploadArea.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        }
    };

    // Enhanced save functions with validation and loading
    $('#saveEditVisit').on('click', function () {
        // if (!validateForm('editVisitForm')) {
        //     Swal.fire('ข้อผิดพลาด', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
        //     return;
        // }

        const formData = new FormData($('#editVisitForm')[0]);
        
        // Show loading
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'api/update_visit_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success) {
                        Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
                        $('#editVisitModal').modal('hide');
                        loadTable();
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    }
                } catch (e) {
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล', 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้: ' + error, 'error');
            }
        });
    });

    $('#saveAddVisit').on('click', function () {
        if (!validateForm('addVisitForm')) {
            Swal.fire('ข้อผิดพลาด', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
            return;
        }

        const formData = new FormData($('#addVisitForm')[0]);
        
        // Show loading
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'api/save_visit_data.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.success) {
                        Swal.fire('สำเร็จ', res.message, 'success');
                        $('#addVisitModal').modal('hide');
                        loadTable();
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล', 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้: ' + error, 'error');
            }
        });
    });

    // Initialize table on page load
    loadTable();
});
</script>
</body>
</html>
