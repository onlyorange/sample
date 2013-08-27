using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PetHealthClub.Controllers;
using PHCDto.DTO;
using PHCData.Data;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;
using PHCBoot.Boot;
using Facebook;
//using ImpactWorks.FBGraph.Connector;
//using ImpactWorks.FBGraph.Core;
//using ImpactWorks.FBGraph.Interfaces;

namespace PetHealthClub.Areas.Admin.Controllers
{
    public class AdController : Controller
    {
        // GET: /Admin/Ad/

        public IChallengePres _ichp { get; set; }

        public ActionResult Index()
        {
            return View();
        }

        [HttpPost]
        public ActionResult Index(LoginDTO dto)
        {
            if (ModelState.IsValid)
            {
                string uname = "admin@gmail.com";
                string pwd = "admin^123";

                if (uname == dto.uname && pwd == dto.pwd)
                {
                    return RedirectToAction("AHome");
                }
                else
                {
                    ModelState.AddModelError("", "Invalid Username/Password.");
                }

            }
            return View();
        }

        public ActionResult AHome()
        {
            return View();
        }

        #region Challenges

        public JsonResult checkchallengename(string cname)
        {
            int cnt = _ichp.chalange_count(cname);
            return Json(cnt, JsonRequestBehavior.AllowGet);
        }

        public ActionResult CreateChallenge()
        {
            List<ChallengeDTO> chdto = new List<ChallengeDTO>();
            chdto = _ichp.GetBadges();
            ViewData["bagdelist"] = chdto;
            return View();
        }

        [HttpPost]
        public ActionResult CreateChallenge(ChallengeDTO dto, FormCollection frm)
        {
            //  if (ModelState.IsValid)
            //  {

            HttpPostedFileBase dogfile = Request.Files["updog"];
            string dog_img = UploadImg(dogfile);

            HttpPostedFileBase catfile = Request.Files["upcat"];
            string cat_img = UploadImg(catfile);

            List<ChallengeDTO> chdto = new List<ChallengeDTO>();
            chdto = _ichp.GetBadges();
            ViewData["bagdelist"] = chdto;

            if (!string.IsNullOrEmpty(dog_img) && !string.IsNullOrEmpty(cat_img))
            {
                _ichp = (ChallengePres)_ichp;

                int cnt = _ichp.chalange_count(dto.ChallengeName);
                if (cnt == 0)
                {

                    ChallengeDTO adto = new ChallengeDTO();
                    adto.ChallengeName = dto.ChallengeName;
                    adto.Description = dto.Description;
                    adto.CatDescription = dto.CatDescription;
                    adto.BID = dto.BID;
                    adto.dogchImg = dog_img;
                    adto.catchImg = cat_img;
                    adto.ChcreatedDate = dto.ChcreatedDate;
                    string msg = _ichp.Save_challenge(adto);

                    if (msg == "success")
                    {
                        TempData["Success"] = "Saved Successfully";
                        return RedirectToAction("Challenges");
                    }
                }
            }
            //  }
            return View();
        }

        public JsonResult returnJsonData(DateTime dt)
        {
            ChallengeDTO ch = new ChallengeDTO();
            ch = _ichp.ChkChallengebyDate(dt);

            return Json(ch, JsonRequestBehavior.AllowGet);
        }


        public ActionResult Challenges()
        {
            List<ChallengeDTO> ldto = new List<ChallengeDTO>();
            ldto = _ichp.Get_challenges_list();
            return View(ldto);
        }
        #endregion

        #region ImgUpload
        public string UploadImg(HttpPostedFileBase file)
        {
            string ImgName = null;
            if (!string.IsNullOrEmpty(file.FileName))
            {
                string Extension = System.IO.Path.GetExtension(file.FileName);
                if (Extension.ToLower() == ".jpg" || Extension.ToLower() == ".gif" || Extension.ToLower() == ".png" || Extension.ToLower() == ".bmp" || Extension.ToLower() == ".jpeg")
                {
                    string extension = System.IO.Path.GetExtension(file.FileName).ToString();

                    if (file.ContentLength > 0)
                    {
                        Random rdm = new Random();
                        string fname = System.IO.Path.GetFileNameWithoutExtension(file.FileName);
                        string no = Convert.ToString(DateTime.Now.Millisecond * rdm.Next(10000));
                        string fileName = fname + "-" + no;

                        fileName = fileName.Replace(" ", "_") + extension;
                        string filePath = System.IO.Path.Combine(HttpContext.Server.MapPath("~/Content/Uploads/Challenges/"), fileName);

                        file.SaveAs(filePath);
                        ImgName = fileName;

                    }
                    else
                    {
                        ModelState.AddModelError("", "Select File");
                        //throw new ApplicationException("Select File");
                    }

                }
                else
                {
                    ModelState.AddModelError("", "Invalid file format.");
                }
            }
            else
            {
                ModelState.AddModelError("", "Browse image to upload.");

            }
            return ImgName;
        }
        #endregion

        #region badge

        public JsonResult checkbadgename(string bname)
        {
            int cnt = _ichp.Badge_count(bname);
            return Json(cnt, JsonRequestBehavior.AllowGet);
        }
        public ActionResult Createbadge()
        {
            return View();
        }

        [HttpPost]
        public ActionResult Createbadge(badgeDTO dto, FormCollection frm)
        {
            if (ModelState.IsValid)
            {
                HttpPostedFileBase badgfile = Request.Files["upbadge"];
                string Badge_logo = UploadImg(badgfile);
                if (!string.IsNullOrEmpty(Badge_logo))
                {
                    _ichp = (ChallengePres)_ichp;
                    int cnt = _ichp.Badge_count(dto.Badgename);
                    if (cnt == 0)
                    {

                        badgeDTO adto = new badgeDTO();
                        adto.Badgename = dto.Badgename;
                        adto.badgeImage = Badge_logo;
                        adto.cdescription = dto.cdescription;
                        adto.ldescription = dto.ldescription;

                        string msg = _ichp.Save_badge(adto);

                        if (msg == "success")
                        {
                            TempData["Success"] = "Saved Successfully";
                            return RedirectToAction("badges");
                        }
                    }
                    else
                    {
                        ModelState.AddModelError("", "Badge name already exist..!");
                    }
                }
            }

            return View();
        }


        public ActionResult badges()
        {
            List<badgeDTO> ldto = new List<badgeDTO>();
            ldto = _ichp.GetBadges_list();
            return View(ldto);

        }
        #endregion

        #region User

        public ActionResult Users()
        {
            List<FbDTO> ldto = new List<FbDTO>();
            ldto = _ichp.users_list();

            return View(ldto);
        }
        #endregion

        //delete user
        public ActionResult Deleteuser(int id)
        {
            string msg = _ichp.deleteuser(id);
            TempData["Success"] = "Deleted Successfully";
            return RedirectToAction("Users");
        }

        //delete challenge
        public ActionResult DeleteCh(int id)
        {
            string msg = _ichp.delete_challenge(id);
            TempData["Success"] = "Deleted Successfully";
            return RedirectToAction("Challenges");
        }

        //edit challenge
        public ActionResult EditCh(int id)
        {
            ChallengeDTO dto = new ChallengeDTO();
            dto = _ichp.Edit_challenge(id);
            List<ChallengeDTO> chdto = new List<ChallengeDTO>();
            chdto = _ichp.GetBadges();
            ViewData["bagdelist"] = chdto;
            return View(dto);
        }

        [HttpPost]
        public ActionResult EditCh(int id, ChallengeDTO dto)
        {
            if (dto != null)
            {
                HttpPostedFileBase file = Request.Files["updog"];
                string dog_img = dto.dogchImg;

                if (Request.Files["updog"] != null && file.FileName != "")
                {
                    //HttpPostedFileBase file = Request.Files["Uploadfile"];
                    dog_img = UploadImg(file);
                    dto.dogchImg = dog_img;
                }

                HttpPostedFileBase file1 = Request.Files["upcat"];
                string cat_img = dto.dogchImg;

                if (Request.Files["upcat"] != null && file1.FileName != "")
                {
                    //HttpPostedFileBase file = Request.Files["Uploadfile"];
                    cat_img = UploadImg(file1);
                    dto.catchImg = cat_img;
                }
                int cnt = _ichp.chalange_count(dto.ChallengeName);
                string chnamehd = dto.chnamehidden;

                if ((chnamehd == dto.ChallengeName && cnt == 1) || cnt == 0)
                {
                    string msg = _ichp.update_challenge(dto);
                    if (msg == "success")
                    {
                        TempData["Success"] = "Updated Successfully";
                        return RedirectToAction("Challenges");
                    }
                }

                else
                {
                    List<ChallengeDTO> chdto = new List<ChallengeDTO>();
                    chdto = _ichp.GetBadges();
                    ViewData["bagdelist"] = chdto;
                    ModelState.AddModelError("", "Challenge name not available");
                }
            }

            return View(dto);
        }

        //edit badge
        public ActionResult EditBadge(int id)
        {
            badgeDTO dto = new badgeDTO();
            dto = _ichp.Edit_badge(id);
            return View(dto);
        }

        //update badge
        [HttpPost]
        public ActionResult EditBadge(int id, badgeDTO dto)
        {

            if (dto != null)
            {
                HttpPostedFileBase file = Request.Files["upbadge"];
                string pet_img = dto.badgeImage;

                if (Request.Files["upbadge"] != null && file.FileName != "")
                {
                    //HttpPostedFileBase file = Request.Files["Uploadfile"];
                    pet_img = UploadImg(file);
                    dto.badgeImage = pet_img;
                }

                string bnamehd = dto.bnamehidden;
                int cnt = _ichp.Badge_count(dto.Badgename);

                if ((bnamehd == dto.Badgename && cnt == 1) || cnt == 0)
                {
                    string msg = _ichp.Update_badge(dto);
                    if (msg == "success")
                    {
                        TempData["Success"] = "Updated Successfully";
                        return RedirectToAction("badges");
                    }
                }
                else
                {

                    ModelState.AddModelError("", "Badge name not available");
                }

            }
            return View(dto);
        }

        //delete badge
        public ActionResult DeleteBadge(int id)
        {
            string msg = _ichp.delete_badge(id);

            TempData["Success"] = "Deleted Successfully";

            return RedirectToAction("badges");


        }


        public ActionResult Wallpaper()
        {
            return View();
        }

        [HttpPost]
        public ActionResult Wallpaper(RewardsDTO dto, FormCollection frm)
        {
            if (ModelState.IsValid)
            {
                HttpPostedFileBase badgfile = Request.Files["upbadge"];
                string WallpaperImage = UploadImg(badgfile);
                if (!string.IsNullOrEmpty(WallpaperImage))
                {
                    RewardsDTO adto = new RewardsDTO();
                    adto.Title = dto.Title;
                    adto.WallImage = WallpaperImage;
                    string msg = _ichp.Save_Wallpaper(adto);
                    if (msg == "success")
                    {
                        TempData["Success"] = "Saved Successfully";
                        return RedirectToAction("WallList");
                    }
                }
            }
            return View();
        }


        public ActionResult WallList()
        {
            List<RewardsDTO> ldto = new List<RewardsDTO>();
            ldto = _ichp.GetWall_list();
            return View(ldto);
        }

        public ActionResult EditWall(int id)
        {
            RewardsDTO dto = new RewardsDTO();
            dto = _ichp.Edit_Wall(id);
            return View(dto);
        }


         [HttpPost]
        public ActionResult EditWall(int id, RewardsDTO dto)
        {
            if (dto != null)
            {
                HttpPostedFileBase file = Request.Files["upbadge"];
                string wall_img = dto.WallImage;

                if (Request.Files["upbadge"] != null && file.FileName != "")
                {

                    wall_img = UploadImg(file);
                    dto.WallImage = wall_img;
                }

              
                    string msg = _ichp.Update_Wall(dto);
                    if (msg == "success")
                    {
                        TempData["Success"] = "Updated Successfully";
                        return RedirectToAction("WallList");
                    }
              
              

            }
            return View(dto);
        }

        public ActionResult DeleteWall(int id)
        {
            string msg = _ichp.delete_wall(id);

            TempData["Success"] = "Deleted Successfully";

            return RedirectToAction("WallList");
        }


    }
}
