using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PetHealthClub.Areas.PetUser.Models
{
    public class RewardViewModel
    {
        public int BadgeCount { get; set; }

        public bool UnlockedWallpaper { get; set; }
        public bool UnlockedCalendar { get; set; }
        public bool Unlocked3DollarCoupon { get; set; }
        public bool Unlocked10DollarCoupon { get; set; }

        public bool AlreadyClaimed { get; set; }
    }
}