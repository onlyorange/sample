using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;

using PetHealthClub.Controllers;
using PHCService.Service.InterfaceContracts;
using PHCDto.DTO;
using PetHealthClub.Areas.Dashboard.Models;

namespace PetHealthClub.Areas.Dashboard.Controllers
{
    public class DashboardController : BaseController
    {
        public IPetProfilePres _ipres { get; set; }

        /// <summary>
        /// Main action for the dashboard page
        /// </summary>
        /// <returns></returns>
        public ActionResult Index()
        {
            var model = new DashboardViewModel();
            
            var lstch = _ipres.ListChallenge(); // let's get all the current challenges
            for (int i = 0; i < lstch.Count; i++)
            {
                model.Challenges.Add(new DashboardChallengeViewModel()
                {
                    index = (i+1),
                    pch = lstch[i].CHID,
                    pchname = lstch[i].ChallengeName,
                    IsContestChallenge = _ipres.IsContestChallenge(lstch[i].CHID),
                    PrizeName = _ipres.GetRewardNameFromChallengeId(lstch[i].CHID),
                    Tagline = lstch[i].Tagline
                });
            }
            
            var pbcnt = _ipres.GetBadgeCount(UDTO.UID);
            model.BadgeCount = pbcnt.badgecount;

            for (var i = 1; i < 21; i++)
            {
                if( model.BadgeCount >= i )
                    model.BadgeCss.Add("gold");
                else
                    model.BadgeCss.Add("");
            }

            if (HttpContext.Session["ShowFirstBadgeModel"] != null 
                && Convert.ToBoolean(HttpContext.Session["ShowFirstBadgeModel"]) == true)
            {
                model.ShowFirstBadgeModel = true;
                Session.Remove("ShowFirstBadgeModel");
            }

            return View(model);
        }
    }
}
