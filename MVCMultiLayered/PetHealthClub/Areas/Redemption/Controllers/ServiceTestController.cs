using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using PetHealthClub.Areas.Redemption.Helpers;
using PetHealthClub.Helpers;
using PHCService.Service.Core;

namespace PetHealthClub.Areas.Redemption.Controllers
{
    public class ServiceTestController : Controller
    {
        protected RedemptionService service = new RedemptionService();

        public JsonResult TestComputeSignature()
        {
            var f = "sdfsdf";
            var p = "prize1";
            var cb = "http://www.google.ca";

            var expected = "8D9A84149B3018F5089213D2BDADB9194FF26A816B614B4FB451A62CE1AFBC66";

            var queryString = string.Format("f={0}&p={1}&cb={2}", f, p, cb);

            var sig = service.ComputeSignature(queryString, RedemptionHelper.SharedKey);
            return Json(sig, JsonRequestBehavior.AllowGet);
        }
    }
}
