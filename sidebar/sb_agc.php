<ul class="navbar-nav bg-gradient-Chick1 sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="Home.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="sidebar-brand-text mx-3">เกษตรกร</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item ">
        <a class="nav-link" href="Home.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span style="font-size: 1rem;">หน้าหลัก</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="data_agc.php">
            <i class="fas fa-users-cog"></i>
            <span style="font-size: 1rem;">ข้อมูลส่วนตัว</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="data_chick.php">
            <i class="fas fa-cart-plus"></i>
            <span style="font-size: 1rem;">ข้อมูลไก่</span></a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="data_food.php">
            <i class="fas fa-seedling"></i>
            <span style="font-size: 1rem;">ข้อมูลอาหารไก่</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="feeding.php">
            <i class="fas fa-hand-holding"></i>
            <span style="font-size: 1rem;">การให้อาหาร</span></a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="data_sale.php">
            <i class="fas fa-store"></i>
            <span style="font-size: 1rem;">การขาย</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="data_inex.php">
            <i class="fas fa-money-bill-alt"></i>
            <span style="font-size: 1rem;">รายรับ-รายจ่าย</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-clipboard-list"></i>
            <span style="font-size: 1rem;">ออกรายงาน</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="report_datachick_ps.php">ช้อมูลไก่</a>
                <a class="collapse-item" href="report_foodchick_ps.php">ข้อมูลการให้อาหารไก่</a>
                <a class="collapse-item" href="report_salechick_ps.php">ข้อมูลการขาย</a>
                <a class="collapse-item" href="report_InExchick_ps.php">ข้อมูลรายรับ-รายจ่าย</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item ">
        <a class="nav-link" data-toggle="modal" data-target="#logoutModal" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span style="font-size: 1rem;">ออกจากระบบ</span>
        </a>
    </li>
</ul>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ออกจากระบบ</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">คุณแน่ใจที่จะออกจากระบบใช่ไหม</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">ยกเลิก</button>
                <a class="btn btn-danger" href="logout.php">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</div>