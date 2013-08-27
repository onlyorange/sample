using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Configuration;
using PetHealthClub.Helpers;

namespace PetHealthClub.Areas.Redemption.Helpers
{
    public class RedemptionHelper
    {
        public static string SharedKey
        {
            get { return ConfigurationHelper.GetLocalValue("ICGSharedKey"); }
        }

        public static string ThreeDollarCouponToken
        {
            get { return ConfigurationHelper.GetLocalValue("ICGPrizeToken_ThreeDollarCoupon"); } 
        }

        public static string DogTagToken
        {
            get { return ConfigurationHelper.GetLocalValue("ICGPrizeToken_DogTag"); }
        }

        public static string TenDollarCouponToken
        {
            get { return ConfigurationHelper.GetLocalValue("ICGPrizeToken_TenDollarCoupon"); }
        }

        public static string GiftCardToken
        {
            get { return ConfigurationHelper.GetLocalValue("ICGPrizeToken_GiftCard"); }
        }
    }
}