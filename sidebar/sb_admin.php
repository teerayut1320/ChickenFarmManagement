<ul class="navbar-nav bg-gradient-Chick1 sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ผู้ดูแลนะบบ</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item ">
        <a class="nav-link" href="Home.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span style="font-size: 1rem;">หน้าหลัก</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="agriculturist.php">
            <i class="fas fa-users-cog"></i>
            <span style="font-size: 1rem;">ข้อมูลเกษตรกร</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true"
            aria-controls="collapseTwo">
            <i class="fas fa-clipboard-list"></i>
            <span style="font-size: 1rem;">ออกรายงานภาพรวม</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="report_Chick_dataAll.php">ช้อมูลไก่</a>
                <a class="collapse-item" href="report_Chick_foodAll.php">ข้อมูลการให้อาหารไก่</a>
                <a class="collapse-item" href="report_Chick_saleAll.php">ข้อมูลการขาย</a>
                <a class="collapse-item" href="report_Chick_InExAll.php">ข้อมูลรายรับ-รายจ่าย</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-clipboard-list"></i>
            <span style="font-size: 1rem;">ออกรายงานรายบุคคล</span>
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