using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
//Access Data From dll's
using PHCDto.DTO;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;
using PHCRepos.Repos.Core;
using PHCRepos.Repos.AbstractContracts;
using PetHealthClub.Models;
using Facebook;
using PetHealthClub.Helpers;

namespace PetHealthClub.Controllers
{

    public class HomeController : Controller
    {
        private static FbDTO adto;
        public IFbprofilePres _ifbp { get; set; }
        public IPetProfilePres _petService { get; set; }
        
        public static readonly string USER = "User";

        #region Facebook helpers

        string FB_APP_ID = ConfigurationHelper.FacebookAppId;
        string FB_SECRET = ConfigurationHelper.FacebookAppSecret;
        string FB_REDIRECT_URL = ConfigurationHelper.FacebookAppUrl +  "/Home/FacebookCallback/";
        
        string FB_AUTH_TOKEN_KEY = "FB_AUTH_TOKEN";
        string FB_USER_DATA_KEY = "SignupModel";

        #endregion

        #region Index
        public ActionResult Index()
        {
            //if (ControllerContext.HttpContext.Request.Cookies.Count == 0)
            //    return RedirectToAction("NoCookies");
            //// Safari/IE cookie issues
            //var userAgent = ControllerContext.HttpContext.Request.UserAgent.ToLower();
            //if (!userAgent.Contains("chrome") && userAgent.Contains("safari") )
            //{
            //    if (Request.QueryString["start_session"] != null) // redirect to the full app experience
            //        return Redirect(ConfigurationHelper.FacebookAppUrl);

            //    if (Request.QueryString["sid"] == null)
            //        return Redirect(ConfigurationHelper.SiteUrl + "/?sid=" + ControllerContext.HttpContext.Session.SessionID );
            //    var sid = ControllerContext.HttpContext.Session.SessionID;
            //    if (string.IsNullOrWhiteSpace(sid) || Request.QueryString["sid"] != sid)
            //    {
            //        Response.Write("<script>");
            //        Response.Write("top.window.location='?start_session=true';");
            //        Response.Write("</script>");
            //        Response.End();
            //        return View();
            //    }
            //}
            
            adto = null;
            ViewBag.appId = FB_APP_ID;
            ViewBag.SkipFbLogin = Request.QueryString["forcesignup"] == null ? false : true;

            var model = new SignupViewModel();
            model.PetType = new SelectList(new Dictionary<int, string>()
            {
                { 0, "Type" },    
                { 1, "Dog" },
                { 2, "Cat" }
            }
            , "key", "value");

            return View(model);
        }
        
        /// <summary>
        /// Basic action for when we can't set a cookie to the user's browser
        /// </summary>
        /// <returns></returns>
        public ActionResult NoCookies()
        {
            return View();
        }

        [HttpPost]
        public ActionResult Signup(SignupPostModel model)
        {
            Response.AddHeader("P3P:CP", "IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT");
            Session[FB_USER_DATA_KEY] = model;
            ViewBag.Redirect = "https://www.facebook.com/dialog/oauth?"
                + "client_id=" + FB_APP_ID
                + "&redirect_uri=" + FB_REDIRECT_URL
                + "&scope=email"
                + "&state=" + Guid.NewGuid().ToString();
            return View();
        }

        #endregion

        #region Facebook related

        /// <summary>
        /// Determines if the user has currently authorized our app
        /// </summary>
        /// <returns></returns>
        public bool UserAuthorizedApp() {
            if (Session[FB_AUTH_TOKEN_KEY] != null) {
                var client = new FacebookClient(Session[FB_AUTH_TOKEN_KEY].ToString());
                try
                {
                    var result = client.Get("/me");
                    if (result != null)
                        return true;
                }
                catch // can ignore this as we'll just invalidate our session token
                {
                }

                Session[FB_AUTH_TOKEN_KEY] = null;
            }
            return false;
        }

        /// <summary>
        /// Callback after the initial facebook oauth request
        /// </summary>
        /// <returns></returns>
        public ActionResult FacebookCallback() {
            
            Response.AddHeader("P3P:CP", "IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT");

            var code = Request.QueryString["code"].Replace("#_=_", "");
            var fbUrl = "https://graph.facebook.com/oauth/access_token?client_id=" + FB_APP_ID
                + "&redirect_uri=" + FB_REDIRECT_URL
                + "&client_secret=" + FB_SECRET
                + "&code=" + code;

            var result = new System.Net.WebClient().DownloadString(fbUrl);
            var token = result.Split('&')[0].Split('=')[1];

            // store token in user data
            Session[FB_AUTH_TOKEN_KEY] = token;

            // get the current user
            var fbClient = new FacebookClient(token);
            dynamic fbUser = fbClient.Get("/me");

            // convert the user information into our data model
            FbDTO user = Save_Update_profile(new FbDTO()
            {
                fbid = fbUser.id,
                Username = String.IsNullOrWhiteSpace(fbUser.username) ? fbUser.id : fbUser.username,
                Email = fbUser.email,
                Fname = fbUser.first_name,
                Lname = fbUser.last_name,
                Sex = fbUser.gender,
                acctoken = token
            });

            // convert our pet signup model and save
            var model = Session[FB_USER_DATA_KEY] as SignupPostModel;
            var petProfile = _ifbp.SavePetDetSignUp(new PetProfileDTO() {
                PetName = model.PetName,
                //PetBreed = model.PetBreed,
                pcid = model.PetType,
                UID = user.UID
            });

            // Award this first badge
            //
            // TODO: Ensure user never gets this badge more than once
            //
            _petService.SaveFirstBadge(user.UID, petProfile.PID);

            // get the badge count

            var pbcnt = _petService.GetBadgeCount(user.UID);
            if (pbcnt.badgecount == 1)
                HttpContext.Session["ShowFirstBadgeModel"] = true;
            else
                HttpContext.Session.Remove("ShowFirstBadgeModel");
            
            // Stash our user in the session
            HttpContext.Session[USER] = adto;

            

            // hack to remove the facebook appened #_-_
            return RedirectToAction("Index", "Home");
        }

        /// <summary>
        /// Sets the token for this user and directs them to the dashboard page
        /// </summary>
        /// <param name="token"></param>
        /// <param name="fbId"></param>
        /// <returns></returns>
        public ActionResult FacebookSetToken(string token, string fbId) {

            Response.AddHeader("P3P:CP", "IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT");
            
            var userDTO = _ifbp.GetUserByFacebookId(fbId);
            if (userDTO == null || userDTO.fbid == null) // force the user to sign up again
                return Redirect( "~/Home/Index/?forcesignup=1");
            
            var client = new FacebookClient(token);
            try
            {
                dynamic user = client.Get("/me");
            }
            catch
            {
                // TODO: log this?
                throw new Exception("Error connecting with facebook");
            }
            
            // Ensure token is updated
            userDTO.acctoken = token;
            _ifbp.update_user(userDTO);

            // Stash in session and redirect
            HttpContext.Session[USER] = userDTO;
            return Redirect("~/Dashboard");
            //return Redirect("~/PetUser/User/BadgePage1");
        }

        #endregion


        #region old..

        public ActionResult Userlogin()
        {
            if (adto != null)
            {
                _ifbp = (FbprofilePres)_ifbp;
                PetProfileDTO dt = new PetProfileDTO();
                HttpContext.Session[USER] = adto;
                return RedirectToAction("Index", "User", new { area = "PetUser" });
            }
            else
            {
                return RedirectToAction("Index");
            }
        }

        public ActionResult savedet(string id, string MN)
        {

            string fname = id;
            // string mname = "";       //0
            string lname = "";       //0
            string sex = "";         //1

            string fbid = "";        //2
            string token = "";       //3

            #region Retrieve Email


            string email = "";
            string details = "";

            string[] Retrieveemail = MN.Split('~');
            int m = 0;
            foreach (string e in Retrieveemail)
            {
                if (m == 0)
                {
                    details = e;
                    m++;
                }
                else
                {
                    email = e;
                    m = 0;
                }
            }

            #endregion

            #region Retrieve Details


            string[] RetrieveDetails = details.Split('?');
            foreach (string d in RetrieveDetails)
            {
                if (m == 0)
                {
                    lname = d;
                    m++;
                }
                else if (m == 1)
                {
                    sex = d;
                    m++;
                }
                else if (m == 2)
                {
                    fbid = d;
                    m++;
                }
                else if (m == 3)
                {
                    token = d;
                    m = 0;
                }


                else
                    m = 0;
            }


            #endregion



            FbDTO dto = new FbDTO();
            dto.Email = email;
            dto.Fname = fname;
            dto.Lname = lname;
            dto.Sex = sex;


            dto.fbid = fbid;
            dto.acctoken = token;

            Save_Update_profile(dto);
            return View();
        }

        private FbDTO Save_Update_profile(FbDTO dto)
        {
            if (dto != null)
            {
                _ifbp = (FbprofilePres)_ifbp;
                int cnt = _ifbp.userCount(dto);
                if (cnt == 0)
                {
                    FbDTO fb = _ifbp.Save_user(dto);
                    if (fb != null)
                    {
                        adto = new FbDTO();
                        adto.UID = fb.UID;
                        adto.Fname = fb.Fname;
                        adto.Lname = fb.Lname;
                        adto.Sex = fb.Sex;
                        adto.Email = fb.Email;
                        adto.acctoken = fb.acctoken;
                        adto.fbid = fb.fbid;
                        adto.rcnt = 1;

                    }
                }
                else
                {
                    FbDTO fb = _ifbp.update_user(dto);

                    if (fb != null)
                    {
                        adto = new FbDTO();
                        adto.UID = fb.UID;
                        adto.Fname = fb.Fname;
                        adto.Lname = fb.Lname;
                        adto.Sex = fb.Sex;
                        adto.Email = fb.Email;
                        adto.acctoken = fb.acctoken;
                        adto.fbid = fb.fbid;
                        adto.rcnt = 2;
                    }
                }
            }
            return adto;
        }

        public ActionResult gotologout()
        {
            Session["User"] = null;
            return RedirectToAction("Index");
        }
        #endregion
    }
}
