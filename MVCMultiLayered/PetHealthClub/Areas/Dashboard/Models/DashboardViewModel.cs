using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PetHealthClub.Areas.Dashboard.Models
{
    /// <summary>
    /// ViewModel for our dashboard display
    /// </summary>
    public class DashboardViewModel
    {
        public List<DashboardChallengeViewModel> Challenges { get; set; }
        public bool ShowFirstBadgeModel { get; set; }
        public int BadgeCount { get; set; }
        public List<string> BadgeCss { get; set; }

        public DashboardViewModel()
        {
            Challenges = new List<DashboardChallengeViewModel>();
            BadgeCss = new List<string>();
        }
    }

    public class DashboardChallengeViewModel
    {
        public int index { get; set; }
        public int pch { get; set; }
        public string pchname { get; set; }
        public bool IsContestChallenge { get; set; }
        public string PrizeName { get; set; }
        public string Tagline { get; set; }
    }
}