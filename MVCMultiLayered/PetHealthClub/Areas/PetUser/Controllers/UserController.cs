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
using Twitterizer;
using System.Configuration;
using Twitterizer.Core;
//using Twitterizer.Framework;
using PetHealthClub.Areas.PetUser.Models;
using PetHealthClub.Helpers;
using System.Web.UI;
using System.IO;
using System.Drawing;


namespace PetHealthClub.Areas.PetUser.Controllers
{
    public class UserController : BaseController
    {       
        // GET: /PetUser/User/
        public IPetProfilePres _ipres { get; set; }
        public IFbprofilePres _userService { get; set; }
        public IRedemptionService _redemption { get; set; }

        private static int tst = 1;
        List<FacebookFriend> lstdto = new List<FacebookFriend>();

        public ActionResult Index()
        {
                     
            if (UDTO.UID != 0)
            {
              int ucnt=_ipres.chkuser(UDTO.UID);
              if (ucnt == 0)
              {
                  return View();
              }
              else
              {
                  return RedirectToAction("BadgePage1", "User", new { area = "PetUser" });
              }
                
            }
            else
            {
                return RedirectToAction("../../Home/Index");
            }
           
        }

        public ActionResult BadgePage(string pname, string pbreed,string pcat)
        {
            ProfileDTO dto = new ProfileDTO();
            try
            {              
                dto.PetName = pname;
                dto.PetBreed = pbreed;
                dto.pcid = Convert.ToInt32(pcat);
                dto.UID = UDTO.UID;
                PetProfileDTO pdto = _ipres.SaveFirstPet(dto);
                dto.PID = pdto.PID;
                int bcnt=0;
                bcnt = _ipres.SaveFirstBadge(UDTO.UID,dto.PID);
              
                List<ChallengeDTO> lstch = new List<ChallengeDTO>();
                lstch = _ipres.ListChallenge();

                dto.pch1 = 0;
                dto.pch2 = 0;
                dto.pch3 = 0;
                dto.pch4 = 0;
                dto.pch5 = 0;
                dto.pch6 = 0;

                for (int i = 0; i < lstch.Count; i++)
                {
                    if (i == 0)
                    {
                        dto.pch1 = lstch[i].CHID;
                        dto.pchname1 = lstch[i].ChallengeName;
                    }
                    else if (i == 1)
                    {
                        dto.pch2 = lstch[i].CHID;
                        dto.pchname2 = lstch[i].ChallengeName;
                    }
                    else if (i == 2)
                    {
                        dto.pch3 = lstch[i].CHID;
                        dto.pchname3 = lstch[i].ChallengeName;
                    }
                    else if (i == 3)
                    {
                        dto.pch4 = lstch[i].CHID;
                        dto.pchname4 = lstch[i].ChallengeName;
                    }
                    else if (i == 4)
                    {
                        dto.pch5 = lstch[i].CHID;
                        dto.pchname5 = lstch[i].ChallengeName;
                    }
                    else if (i == 5)
                    {
                        dto.pch6 = lstch[i].CHID;
                        dto.pchname6 = lstch[i].ChallengeName;
                    }
                }

                ProfileDTO pbcnt = new ProfileDTO();
               
                pbcnt = _ipres.GetBadgeCount(UDTO.UID);

                dto.badgecount = pbcnt.badgecount;

                string token = UDTO.acctoken;
                var client = new Facebook.FacebookClient(token);
                dynamic fbresult = client.Get("me/friends");
                var data = fbresult["data"].ToString();
                dto.friendsListing = Newtonsoft.Json.JsonConvert.DeserializeObject<List<FacebookFriend>>(data);
                return View(dto);
                // constants
            }
            catch (Exception ex)
            {
                string message = "token has closed or you are logged out ";
                return RedirectToAction("../../Home/Index");
            }
        }

        [HttpPost]
        public ActionResult BadgePage(ProfileDTO dto, FormCollection Form)
        {
            return RedirectToAction("PetProfile", "User",null);
        }

        public ActionResult BadgePage1()
        {
            return RedirectToAction("", "Dashboard", new { area = "Dashboard" });

            //
            // Rediecting this to the 
            //

            ProfileDTO dto = new ProfileDTO();
            try
            {
                List<ChallengeDTO> lstch = new List<ChallengeDTO>();
                lstch = _ipres.ListChallenge();

                dto.pch1 = 0;
                dto.pch2 = 0;
                dto.pch3 = 0;
                dto.pch4 = 0;
                dto.pch5 = 0;
                dto.pch6 = 0;

              //  dto.PID = id;

                for (int i = 0; i < lstch.Count; i++)
                {
                    if (i == 0)
                    {
                        dto.pch1 = lstch[i].CHID;
                        dto.pchname1 = lstch[i].ChallengeName;
                    }
                    else if (i == 1)
                    {
                        dto.pch2 = lstch[i].CHID;
                        dto.pchname2 = lstch[i].ChallengeName;
                    }
                    else if (i == 2)
                    {
                        dto.pch3 = lstch[i].CHID;
                        dto.pchname3 = lstch[i].ChallengeName;
                    }
                    else if (i == 3)
                    {
                        dto.pch4 = lstch[i].CHID;
                        dto.pchname4 = lstch[i].ChallengeName;
                    }
                    else if (i == 4)
                    {
                        dto.pch5 = lstch[i].CHID;
                        dto.pchname5 = lstch[i].ChallengeName;
                    }
                    else if (i == 5)
                    {
                        dto.pch6 = lstch[i].CHID;
                        dto.pchname6 = lstch[i].ChallengeName;
                    }
                }

                ProfileDTO pbcnt = new ProfileDTO();
                pbcnt = _ipres.GetBadgeCount(UDTO.UID);

                dto.badgecount = pbcnt.badgecount;

                string token = UDTO.acctoken;
                var client = new Facebook.FacebookClient(token);
                dynamic fbresult = client.Get("me/friends");
                var data = fbresult["data"].ToString();
                dto.friendsListing = Newtonsoft.Json.JsonConvert.DeserializeObject<List<FacebookFriend>>(data);
                return View(dto);
                // constants
            }
            catch (Exception ex)
            {
                string message = "token has closed or you are logged out ";
                return RedirectToAction("../../Home/Index");
            }
        }

        [HttpPost]
        public ActionResult BadgePage1(ProfileDTO dto, FormCollection Form)
        {
            return RedirectToAction("PetProfile", "User",null);
        }

        public ActionResult PetProfile()
        {
            ProfileDTO dto = new ProfileDTO();
            dto = _ipres.GetLastPetReg(UDTO.UID);
            if (dto != null)
            {
                int cnt = dto.ldto.Count;
                if (HttpContext.Session["ShowBadgeModal"] != null)
                {
                    HttpContext.Session.Remove("ShowBadgeModal");
                    ViewBag.ShowBadgeModal = true;
                    ViewBag.PetImage = dto.PetImage;
                }
                return View(dto);
            }
            else
            {
                ProfileDTO pd = new ProfileDTO();
                return View(pd);
            }
        }

        [HttpPost]
        public ActionResult PetProfile(ProfileDTO pdto, string Save, string Update)
        {
          
            if (!string.IsNullOrEmpty(Save))
            {
                string pet_img1 = null;
                if (Request.Files["Uploadfile2"] != null)
                {
                    HttpPostedFileBase file1 = Request.Files["Uploadfile2"];
                    pet_img1 = UploadImg2(file1);
                    if (!string.IsNullOrEmpty(pet_img1))
                    {
                        if (UDTO != null)
                        {
                            pdto.UID = UDTO.UID;

                            pdto.pimg1 = pet_img1;
                            int p = _ipres.savepetprofile(pdto);
                            TempData["Success"] = "Saved Successfully";
                            return RedirectToAction("PetProfile", "User", new { area = "PetUser" });
                        }
                    }
                }

            }
            if (!string.IsNullOrEmpty(Update))
            {
                HttpPostedFileBase file = Request.Files["Uploadfile"];
                string pet_img = pdto.pimg;
                if (Request.Files["Uploadfile"] != null && file.FileName != "")
                {               
                    pet_img = UploadImg(file);
                }

                if (!string.IsNullOrEmpty(pet_img))
                {
                    if (UDTO != null)
                    {
                        pdto.UID = UDTO.UID;                   
                        pdto.PetImage = pet_img;
                        
                        // cache the status of our current badge value
                        var hasBadge = _ipres.UserHasBadge(UDTO.UID, 22);

                        int p = _ipres.Update_petProfile(pdto);

                        if (hasBadge != _ipres.UserHasBadge(UDTO.UID, 22)) // if they now have the badge, let's show the earned modal on the next screen
                            HttpContext.Session["ShowBadgeModal"] = true;
                        
                        TempData["Success"] = "Updated Successfully";
                        return RedirectToAction("PetProfile", "User", new { area = "PetUser" });
                        
                    }
                    else { return View(pdto); }
                }
                else
                {
                    ModelState.AddModelError("", "Please upload photo");
                    return View(pdto);
                }
            }
            else { return View(pdto); }
        }

        #region ImgUpload
        public string UploadImg(HttpPostedFileBase file)
        {
            string ImgName = null;
            if (!string.IsNullOrEmpty(Request.Files["Uploadfile"].FileName))
            {
                string Extension = System.IO.Path.GetExtension(Request.Files["Uploadfile"].FileName);
                if (Extension.ToLower() == ".jpg" || Extension.ToLower() == ".gif" || Extension.ToLower() == ".png" || Extension.ToLower() == ".bmp" || Extension.ToLower() == ".jpeg")
                {
                    string extension = System.IO.Path.GetExtension(Request.Files["Uploadfile"].FileName).ToString();

                    if (Request.Files["Uploadfile"].ContentLength > 0)
                    {
                        Random rdm = new Random();
                        string fname = System.IO.Path.GetFileNameWithoutExtension(Request.Files["Uploadfile"].FileName);
                        string no = Convert.ToString(DateTime.Now.Millisecond * rdm.Next(10000));
                        string fileName = fname + "-" + no;

                        fileName = fileName.Replace(" ", "_") + extension;
                        string filePath = System.IO.Path.Combine(HttpContext.Server.MapPath("~/Content/Uploads"), fileName);

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


        public string UploadImg2(HttpPostedFileBase file)
        {
            string ImgName = null;
            if (!string.IsNullOrEmpty(Request.Files["Uploadfile2"].FileName))
            {
                string Extension = System.IO.Path.GetExtension(Request.Files["Uploadfile2"].FileName);
                if (Extension.ToLower() == ".jpg" || Extension.ToLower() == ".gif" || Extension.ToLower() == ".png" || Extension.ToLower() == ".bmp" || Extension.ToLower() == ".jpeg")
                {
                    string extension = System.IO.Path.GetExtension(Request.Files["Uploadfile2"].FileName).ToString();

                    if (Request.Files["Uploadfile2"].ContentLength > 0)
                    {
                        Random rdm = new Random();
                        string fname = System.IO.Path.GetFileNameWithoutExtension(Request.Files["Uploadfile2"].FileName);
                        string no = Convert.ToString(DateTime.Now.Millisecond * rdm.Next(10000));
                        string fileName = fname + "-" + no;

                        fileName = fileName.Replace(" ", "_") + extension;
                        string filePath = System.IO.Path.Combine(HttpContext.Server.MapPath("~/Content/Uploads"), fileName);
                        
                        //file.SaveAs(filePath);
                        //var fileUrl = Url.Content("~/Content/Uploads/" + fileName);
                        //var result = new System.Net.WebClient().DownloadString(fileUrl + "?maxwidth=300&maxheight=300");

                        //var filestream = 
                        //
                        // Resize photo
                        //

                        //file.SaveAs(filePath);

                        //var newFile = 

                        //var newWidth = 

                        //Bitmap newImage = new Bitmap(newWidth, newHeight);
                        //using (Graphics gr = Graphics.FromImage(newImage))
                        //{
                        //    gr.SmoothingMode = SmoothingMode.HighQuality;
                        //    gr.InterpolationMode = InterpolationMode.HighQualityBicubic;
                        //    gr.PixelOffsetMode = PixelOffsetMode.HighQuality;
                        //    gr.DrawImage(srcImage, new Rectangle(0, 0, newWidth, newHeight));
                        //}


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

        #region edit ,delete pet

        public ActionResult Delete(int id)
        {
            string msg = _ipres.Delete_pet(id);
            TempData["Success"] = "Deleted Successfully";
            return RedirectToAction("PetProfile");

        }

        public ActionResult Editpetprofile(int id)
        {
            ProfileDTO pdto = _ipres.geteditpetprofile(id);
            return View(pdto);

        }

        [HttpPost]
        public ActionResult Editpetprofile(ProfileDTO pdto)
        {
            if (ModelState.IsValid)
            {
                HttpPostedFileBase file = Request.Files["Uploadfile"];
                string pet_img = pdto.pimg;
                if (Request.Files["Uploadfile"] != null && file.FileName != "")
                {
                    //HttpPostedFileBase file = Request.Files["Uploadfile"];
                    pet_img = UploadImg(file);
                }

                //   string pet_img = UploadImg(file);

                if (!string.IsNullOrEmpty(pet_img))
                {
                    if (UDTO != null)
                    {
                        pdto.UID = UDTO.UID;
                        //pdto.DOB = Convert.ToDateTime(pdto.DOBDate);
                        pdto.PetImage = pet_img;
                        //new lines of code

                        // cache the status of our current badge value
                        var hasBadge = _ipres.UserHasBadge(UDTO.UID, 22);
                        
                        int p = _ipres.Update_petProfile(pdto);
                        
                        if (hasBadge != _ipres.UserHasBadge(UDTO.UID, 22)) // if they now have the badge, let's show the earned modal on the next screen
                            HttpContext.Session["ShowBadgeModal"] = true;

                        TempData["Success"] = "Updated Successfully";
                        return RedirectToAction("PetProfile", "User", new { area = "PetUser" });
                    }
                    else { return View(pdto); }
                }



                else
                {
                    ModelState.AddModelError("", "Please upload photo");
                    return View(pdto);
                }
            }
            else { return View(pdto); }
        }

        #endregion
                
        public ActionResult PetChallenge(int id)
        {
            if( id == 0 )
                return RedirectToAction("", "Dashboard", new { @chi = id, area = "Dashboard" });

            int chkchcnt = _ipres.ChUserCnt(id, UDTO.UID);
            
            //if( chkchcnt > 0 )
              //  return RedirectToAction("", "Dashboard", new { @chi = id, area = "Dashboard" });

            if (chkchcnt == 0)
                ViewBag.CompletedChallenge = false;
            else
            {
                ViewBag.CompletedChallenge = true;
                var challengeDetail = _ipres.GetEarnedBadgeByChallenge(id, UDTO.UID );
                
                ViewBag.CompletedModel = new ChallengeCompletedViewModel()
                {
                    DateCompleted = challengeDetail.ChcreatedDate,
                    UserDescription = challengeDetail.userdesc,
                    UserImage = challengeDetail.userpetimg
                };
            }

            ChallengeDTO dto = new ChallengeDTO();
            dto = _ipres.GetChallenginfo(id);
            dto.CHID = id;
            dto.ChallengeName=dto.ChallengeName;
            ChallengeDTO bdto = new ChallengeDTO();

            bdto = _ipres.PreviousChallenges();
            dto.lstchdto = bdto.lstchdto;

            List<ChallengeDTO> lstpdto = new List<ChallengeDTO>();
            if (UDTO.UID != 0)
            {
                lstpdto = _ipres.GetPetnames(UDTO.UID, id);

                ViewData["petlist"] = lstpdto;
            }

            return View(dto);
        }

        [HttpPost]
        public ActionResult PetChallenge(ChallengePostModel model )
            //ChallengeDTO dto, FormCollection form)
        {
            HttpPostedFileBase dogfile = Request.Files[model.UPLOAD_FILE_FIELD_NAME];
            string challengePhoto = UploadImg1(dogfile);
           
            ChallengeDTO dt = new ChallengeDTO();
            dt.CHID = model.ChallengeId;
            dt.userpetimg = challengePhoto;
            dt.Description = model.userdesc;
            dt.PID = model.PetId;
            dt.petid = UDTO.UID;
                
            _ipres.SaveChallengeBadge(dt);
      
            return RedirectToAction("BadgesList", "User");                            
        }

        #region ImgUpload
        public string UploadImg1(HttpPostedFileBase file)
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

        public ActionResult test()
        {
            return RedirectToAction("../../Home/Index");
        }

        public ActionResult BadgesList()
        {           
            ChallengeDTO bdto = new ChallengeDTO();
            bdto = _ipres.GetBadges(UDTO.UID);
            return View(bdto);
        }

        public ActionResult Rewards(string id)
        {
            //var dto = _ipres.GetRewards(UDTO.UID);
            var dto = _ipres.GetBadgeCount(UDTO.UID);
            var model = new RewardViewModel()
            {
                 BadgeCount = dto.badgecount,
                 UnlockedWallpaper = dto.badgecount >= 1,
                 UnlockedCalendar = dto.badgecount >= 9,
                 Unlocked3DollarCoupon = dto.badgecount >= 11,
                 Unlocked10DollarCoupon = dto.badgecount >= 14,
                 AlreadyClaimed = (id == "claimed")
            };

            return View(model);
        }

        /// <summary>
        /// Attempts to redeem a particular reward
        /// </summary>
        /// <param name="id"></param>
        /// <returns></returns>
        public ActionResult RedeemReward(int id)
        {
            var isEligable = _redemption.IsEligableForReward(UDTO.UID, id);
            if (!isEligable)
            {
                // pass a message or something
                return RedirectToAction("Rewards/claimed");
            }

            var _service = new RedemptionService();

            // update the reward table to include this reward
            var prize = _service.GetPrize(id);
            _service.AssignPrizeToUser(UDTO.UID, prize.WID);

            // generate the token and pass off to ICG
            var qs = _service.CreateQueryString(
                UDTO.fbid
                , prize.ICGPin
                , ConfigurationHelper.ICGCallbackUrl
            );
            var sig = _service.ComputeSignature(qs, PetHealthClub.Areas.Redemption.Helpers.RedemptionHelper.SharedKey);

            var url = ConfigurationHelper.ICGEndpoint
                + "?" + qs
                + "&s=" + sig
            ;

            return Redirect(url);
        }

        #region invitation
        public ActionResult Addinvitation(string id)
        {
            FacebookFriend fdto = null;
            if (tst == 1)
            {
                HttpContext.Session["fbidlist"] = null;
                fdto = new FacebookFriend();
                fdto.id = id;
                lstdto.Add(fdto);
                HttpContext.Session["fbidlist"] = lstdto;
                tst++;

            }
            else
            {
                lstdto = HttpContext.Session["fbidlist"] as List<FacebookFriend>;

                fdto = new FacebookFriend();
                fdto.id = id;
                lstdto.Add(fdto);
                HttpContext.Session["fbidlist"] = lstdto;
            }
            return View();
        }

        public ActionResult Removeinvitation(string id)
        {
            lstdto = HttpContext.Session["fbidlist"] as List<FacebookFriend>;

            FacebookFriend frnd = new FacebookFriend();
            var dt = lstdto.Where(p => p.id == id).SingleOrDefault();
            frnd.id = dt.id;
            lstdto.Remove(dt);

            HttpContext.Session["fbidlist"] = lstdto;
            return View();
        }
        #endregion


        public void Dashboard()
        {
            Response.Redirect("~/PetUser/User/BadgePage");
        }

        public ActionResult mtest()
        {
            return View();
        }

        public ActionResult gotologout()
        {
            Session["User"] = null;
            return RedirectToAction("../../Home/Index");
        }

        public JsonResult Chkpetbadge(int pid,int cid)
        {
            ChallengeDTO ch = new ChallengeDTO();
            ch = _ipres.Chkpetbadge(pid,cid);

            return Json(ch, JsonRequestBehavior.AllowGet);
        }

        #region TwitterIntegration

        public ActionResult TwitterHome()
        {
            int ebid = 0;
            if (Request.QueryString["ebid"] != null)
            {
                ebid = Convert.ToInt32(Request.QueryString["ebid"]);
                Session["ebid"] = ebid;
            }

            bool Access;
            if (this.GetCachedAccessToken() == null)
            {
                Access = false;
            }
            else
            {
                Access = true;
                string id = "My pets and I just joined @NaturesRecipe #PetHealthClub and got free, healthy pet food from Nature's Recipe";
                savetweet(id);
                int eid = Convert.ToInt32(Session["ebid"]);
                _ipres.TweetUpdate(eid);
            }
            ViewData["Access"] = Access;
            return View();
        }

        [HttpPost]
        public ActionResult TwitterHome(FormCollection frm)
        {
            return View();
        }

        public void savetweet(string id)
        {
            try
            {

                Twitterizer.TwitterStatus.Update(this.GetCachedAccessToken(), id.Trim());
            }
            catch (Twitterizer.TwitterizerException tex)
            {
                string err = "There was an error working with Twitter. Check the username/password in Web.config." + System.Environment.NewLine + System.Environment.NewLine + tex.Message;
                throw tex;
            }
            catch (Exception ex)
            {
                throw ex;
            }
        }

        public void logintweet()
        {
            Response.Redirect(this.GetTwitterAuthorizationUrl());

        }

        public ActionResult TweetCallback()
        {


            if (!string.IsNullOrEmpty(Request.QueryString["oauth_token"]))
            {
                this.CreateCachedAccessToken(Request.QueryString["oauth_token"]);
                return RedirectToAction("TwitterHome", "User", new { area = "PetUser" });

            }
            return View();

        }

        protected OAuthTokens GetCachedAccessToken()
        {
            if (Session["AccessToken"] != null)
            {
                return (OAuthTokens)Session["AccessToken"];
            }
            else
            {
                return null;
            }
        }

        public ulong GetCachedUserId()
        {
            if (Session["GetCachedUserId"] != null)
            {
                return Convert.ToUInt64(Session["GetCachedUserId"]);
            }
            else
            {
                return ulong.MinValue;
            }
        }

        protected void CreateCachedAccessToken(string requestToken)
        {
            //string ConsumerKey = ConfigurationManager.AppSettings["ConsumerKey"];
            //string ConsumerSecret = ConfigurationManager.AppSettings["ConsumerSecret"];

            //OAuthTokenResponse responseToken = OAuthUtility.GetAccessToken(ConsumerKey, ConsumerSecret, requestToken);

            ////Cache the UserId
            //Session["GetCachedUserId"] = responseToken.UserId;

            //OAuthTokens accessToken = new OAuthTokens();
            //accessToken.AccessToken = responseToken.Token;
            ////accessToken.AccessToken = ConfigurationManager.AppSettings["Access token"];
            //accessToken.AccessTokenSecret = responseToken.TokenSecret;
            ////accessToken.AccessTokenSecret = ConfigurationManager.AppSettings["Access token secret"];
            //accessToken.ConsumerKey = ConsumerKey;
            //accessToken.ConsumerSecret = ConsumerSecret;
            //Session["AccessToken"] = accessToken;
        }

        protected string GetTwitterAuthorizationUrl()
        {
            //string ConsumerKey = ConfigurationManager.AppSettings["ConsumerKey"];
            //string ConsumerSecret = ConfigurationManager.AppSettings["ConsumerSecret"];

            //OAuthTokenResponse reqToken = OAuthUtility.GetRequestToken(ConsumerKey, ConsumerSecret);
            //return "https://api.twitter.com/oauth/authorize?oauth_token=" + reqToken.Token;
            return "";
        }

        #endregion
       
        public JsonResult CompleteCh(int ebid)
        {
            return Json(_ipres.CompleteCh(ebid), JsonRequestBehavior.AllowGet);
        }

       // InCompleteCh
        public JsonResult InCompleteCh(int bid)
        {
            ChallengeDTO ch = new ChallengeDTO();
            ch = _ipres.InCompleteCh(bid);

            return Json(ch, JsonRequestBehavior.AllowGet);
        }

       // petCompleteCh
        public JsonResult petCompleteCh(int ebid)
        {
            ChallengeDTO ch = new ChallengeDTO();
            ch = _ipres.petCompleteCh(ebid);

            return Json(ch, JsonRequestBehavior.AllowGet);
        }

       // GetRewards
        public JsonResult GetRewards()
        {
            ProfileDTO badgerewards = new ProfileDTO();
            badgerewards = _ipres.GetRewards(UDTO.UID);
            return Json(badgerewards, JsonRequestBehavior.AllowGet);
        }

       // chkchallinfo
        public JsonResult chkchallinfo(int petid, int chid)
        {
            ChallengeDTO chinfo = new ChallengeDTO();
            chinfo = _ipres.chkchallinfo(petid,chid);
            return Json(chinfo, JsonRequestBehavior.AllowGet);
        }

       // CHNotify
        public JsonResult CHNotify()
        {
            ChallengeDTO ch = new ChallengeDTO();
            ch = _ipres.CHNotify(UDTO.UID);
            return Json(ch, JsonRequestBehavior.AllowGet);
        }

       // CHDExists
        public JsonResult CHDExists(int chi) {
            var p = _ipres.GetEarnedBadgeByChallenge(chi, UDTO.UID);
            return Json(p, JsonRequestBehavior.AllowGet);
        }


        public ActionResult PinUpdate(int ebid)
        {
            _ipres.PinUpdate(ebid);
            return RedirectToAction("BadgesList","User",null);
        }

        public ActionResult WallpaperList()
        {
            RewardsDTO wdto = new RewardsDTO();
            wdto = _ipres.GetWallPaperList();
            return View(wdto);
        }

        public ActionResult ForceImageDownload(int? wid)
        {

            int w =Convert.ToInt32(wid);

            string ImgURL=_ipres.UpdateRewards(UDTO.UID,w);

            if (ImgURL != null)
            {

                string folder = "~/Content/Uploads/Challenges/" + ImgURL;

                string path = HttpContext.Server.MapPath("~/Content/Uploads/Challenges/" + ImgURL);

                string type = "";

                string name = System.IO.Path.GetFileName(ImgURL);
                string ext = System.IO.Path.GetExtension(ImgURL);

                if (ext != null)
                {
                    if (ext.ToLower() == ".png")
                    {
                        type = "image/png";
                    }
                    else if (ext.ToLower() == ".jpg")
                    {
                        type = "image/jpg";
                    }
                    else if (ext.ToLower() == ".gif")
                    {
                        type = "image/gif";
                    }
                    else if (ext.ToLower() == ".bmp")
                    {
                        type = "image/bmp";
                    }
                    else if (ext.ToLower() == ".jpeg")
                    {
                        type = "image/jpeg";
                    }
                }

                string filename = Path.GetFileName(ImgURL);
                Response.AppendHeader("Content-Disposition", "attachment; filename=" + filename);
                return File(path, type);
            }
            else
            {
                return RedirectToAction("WallpaperList");
            }
        }


        #region Share helpers

        /// <summary>
        /// Update the current user as having shared to facebook
        /// </summary>
        /// <returns></returns>
        [HttpPost]
        public JsonResult ShareToFacebook()
        {
            // get the current user from the session and update
            var user = Session[HomeController.USER] as FbDTO;
            user.SharedToFacebookOn = DateTime.UtcNow;
            _userService.UserUpdateForShare(user);
            Session[HomeController.USER] = user;

            return Json(
                GetShareBadgeResponse(user)
                , JsonRequestBehavior.AllowGet
            );
        }
        
        /// <summary>
        /// Update the current user has having shared to twitter
        /// </summary>
        /// <returns></returns>
        [HttpPost]
        public JsonResult ShareToTwitter()
        {
            // get the current user from the session and update
            var user = Session[HomeController.USER] as FbDTO;
            user.SharedToTwitterOn = DateTime.UtcNow;
            _userService.UserUpdateForShare(user);
            Session[HomeController.USER] = user;

            return Json(
                GetShareBadgeResponse(user)
                , JsonRequestBehavior.AllowGet
            );
        }
        
        /// <summary>
        /// Update the current user as having shared to pinterest
        /// </summary>
        /// <returns></returns>
        [HttpPost]
        public JsonResult ShareToPinterest()
        {
            // get the current user from the session and update
            var user = Session[HomeController.USER] as FbDTO;
            user.SharedToPinterestOn = DateTime.UtcNow;
            _userService.UserUpdateForShare(user);
            Session[HomeController.USER] = user;

            return Json(
                GetShareBadgeResponse(user)
                , JsonRequestBehavior.AllowGet
            );
        }

        /// <summary>
        /// Checks to see if the user just shared to a single network and hasn't already earned the badge
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        private bool UnlockedSingleShareBadge(FbDTO user)
        {
            var count = 0;
            if( user.SharedToFacebookOn != null && user.SharedToFacebookOn != DateTime.MinValue )
                count++;
            if (user.SharedToTwitterOn != null && user.SharedToTwitterOn != DateTime.MinValue)
                count++;
            if (user.SharedToPinterestOn != null && user.SharedToPinterestOn != DateTime.MinValue)
                count++;
            
            // If this is the first share, attempt to assign and return the result. if true this means we should notify
            if (count == 1)
                return _ipres.AssignBadge(user.UID, 21, null);
            
            return false;
        }
        
        /// <summary>
        /// Returns true if the user shared to all three services and hasn't yet received the badge
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        private bool UnlockedSuperShareBadge(FbDTO user)
        {
            if (user.SharedToFacebookOn != null && user.SharedToFacebookOn != DateTime.MinValue
                && user.SharedToTwitterOn != null && user.SharedToTwitterOn != DateTime.MinValue
                && user.SharedToPinterestOn != null && user.SharedToPinterestOn != DateTime.MinValue
                )
            {
                return _ipres.AssignBadge(user.UID, 20, null);
            }
            return false;
        }

        /// <summary>
        /// Returns 'single', 'super', or 'na' based on if this user just unlocked a badge
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        private string GetShareBadgeResponse(FbDTO user)
        {
            if (UnlockedSingleShareBadge(user))
                return "single";
            else if (UnlockedSuperShareBadge(user))
                return "super";
            return "na";
        }

        #endregion
    }
}
