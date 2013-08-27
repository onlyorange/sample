using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;
using System.Web.Routing;
using PetHealthClub.Areas.Redemption.Models;
using PHCBoot.Boot;
using PetHealthClub.Helpers;
using PetHealthClub.Areas.Redemption.Helpers;
using System.Net;

namespace PetHealthClub.Areas.Redemption.Controllers
{
    /// <summary>
    /// Controller for handle the redemption service
    /// </summary>
    public class ServiceController : Controller
    {
        private IRedemptionService _service = new RedemptionService();
        public IChallengePres _challegeService { get; set; }
        public IFbprofilePres _profileService { get; set; }

        public ActionResult Index()
        {
            return View();
        }

        /// <summary>
        /// Confirms that a given token has been used to redeem a prize
        /// </summary>
        /// <param name="token"></param>
        /// <returns></returns>
        public JsonResult ConfirmPrize( ConfirmPrizeModel model )
        {
            var path = "";
            if (ConfigurationHelper.IsLocal)
                path = "C:\\logs\\log.txt";
            else
                path = Server.MapPath("~/logs/log.txt");

            if (!System.IO.File.Exists(path))
            {
                var newFilefile = System.IO.File.Create(path);
                newFilefile.Close();
            }
            using (var file = new System.IO.StreamWriter(path, true))
            {
                file.WriteLine("---------------------------");
                file.WriteLine(DateTime.Now.ToString());
                file.WriteLine("f: " + model.f);
                file.WriteLine("p: " + model.p);

                var response = new ConfirmPrizeResponseModel() { Status = "ok" };

                try
                {
                    var user = _profileService.GetUserByFacebookId(model.f);
                    if (user == null || user.UID == 0)
                    {
                        file.WriteLine("error: User not found:");
                        throw new Exception("User not found");
                    }

                    var prize = _service.GetPrizeByPin(model.p);

                    if ( _service.ConfirmPrize(user.UID, prize.WID ) )
                    {
                        file.WriteLine("response: Ok!");
                        return Json(response, JsonRequestBehavior.AllowGet);
                    }
                }
                catch (Exception e)
                {
                    file.WriteLine("error: " + e.Message);
                    return Json(
                        new ConfirmPrizeResponseModel() { Status = "error", Message = e.Message }
                        , JsonRequestBehavior.AllowGet
                    );
                }
                file.WriteLine("error: generic");
            }

            return Json(new ConfirmPrizeResponseModel() { Status = "error" }, JsonRequestBehavior.AllowGet);
        }

        #region Test Helpers
        public ActionResult Test()
        {
            var model = new TestViewModel(_challegeService.users_list());
            return View(model);
        }

        [HttpPost]
        public ActionResult Test(TestPostModel model)
        {
            // update the reward table to include this reward
            var user = _profileService.GetUserByFacebookId(model.FacebookId);
            var prize = _service.GetPrizeByPin( model.PinId );
            _service.AssignPrizeToUser(user.UID, prize.WID);

            // generate the token and pass off to ICG
            var qs = _service.CreateQueryString(
                user.fbid
                , prize.ICGPin
                , ConfigurationHelper.ICGCallbackUrl
            );
            var sig = _service.ComputeSignature( qs, RedemptionHelper.SharedKey );

            var url = "http://pethealthuat.icgrouplp.com/RegCrm.aspx"
                + "?" + qs
                + "&s=" + sig
            ;

            if (ConfigurationHelper.IsLocal)
            {
                Response.Write(url);
                Response.End();
            }

            return Redirect(url);
        }

        public ActionResult TestCallBack()
        {
            return View();
        }
        #endregion

    }
}
