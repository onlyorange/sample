using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PHCService.Helpers
{
    public static class ContestHelper
    {
        #region Contest Ids
        public const int FitAndFancyFreeId = 13;
        
        public const int StayHydratedId = 10;
        public const int PearlyWhitesId = 15;
        
        public const int QualityTimeId = 12;
        public const int HealthySnacksId = 20;

        public const int NailedItId = 16;
        #endregion
    }

    public static class RewardHelper
    {
        #region Contest Rewards
        public static int DogBowlId
        {
            get { return 4; }
        }
        public static int DogTagId
        {
            get { return 5; }
        }
        public static int GiftCardId
        {
            get { return 6; }
        }
        #endregion

        #region Reward Names
        public static string DogBowlName
        {
            get { return "pet bowl"; }
        }
        public static string DogTagName
        {
            get { return "pet tag"; }
        }
        public static string GiftCardName
        {
            get { return "$25 gift card"; }
        }
        #endregion

    }
}