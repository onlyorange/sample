using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Configuration;

namespace PetHealthClub.Helpers
{
    public static class ConfigurationHelper
    {
        #region Base
        public static bool IsLocal
        {
            get { return Convert.ToBoolean(ConfigurationManager.AppSettings.Get("IsLocal")); }
        }
        public static bool IsStaging
        {
            get { return Convert.ToBoolean(ConfigurationManager.AppSettings.Get("IsStaging")); }
        }

        public static string GetLocalValue(string key)
        {
            if (IsLocal)
                key += "_Local";
            else if (IsStaging && (ConfigurationManager.AppSettings[key + "_Staging"] != null) )
                key += "_Staging";
            
            return ConfigurationManager.AppSettings[key];
        }
        #endregion

        public static string SiteUrl
        {
            get { return GetLocalValue("SiteUrl"); }
        }

        #region Facebook Helpers

        public static string FacebookAppId
        {
            get { return GetLocalValue("FBAPIKey"); }
        }
        public static string FacebookAppSecret
        {
            get { return GetLocalValue("FBSecretKey"); }
        }
        public static string FacebookAppUrl
        {
            get { return GetLocalValue("FBAppUrl"); }
        }
        #endregion

        #region Amazon AWS Helpers

        public static string AWSS3Bucket
        {
            get { return GetLocalValue("AWSS3Bucket"); }
        }


        #endregion

        #region ICGroup Helpers

        public static string ICGCallbackUrl
        {
            get { return SiteUrl + "/Dashboard"; }
        }

        /// <summary>
        /// The endpoint of the ICG service
        /// </summary>
        public static string ICGEndpoint
        {
            get { return GetLocalValue("ICGEndpoint"); }
        }
        #endregion

        #region Share Copy

        public static string ShareCopy_Facebook_Main
        {
            get { return "My pets and I just joined Nature’s Recipe’s Pet Health Club! We’re earning prizes and living a healthier life!"; }
        }
        public static string ShareCopy_Twitter_Main
        {
            get { return "My pets and I just joined @NaturesRecipe #PetHealthClubPetHealthClub. We're earning rewards and getting healthier!"; }
        }
        public static string ShareCopy_Pinterest_Main
        {
            get { return ConfigurationHelper.ShareCopy_Facebook_Main; }
        }

        // Earned badge
        public static string ShareCopy_Facebook_EarnedBadge
        {
            get { return "I just unlocked a badge from Pet Health Club! I’m winning great prizes and getting healthier with my pet!"; }
        }
        public static string ShareCopy_Twitter_EarnedBadge
        {
            get { return "I just unlocked a #PetHealthClub badge with my pet! Join the club and you earn rewards from @NaturesRecipe!"; }
        }
        public static string ShareCopy_Pinterest_EarnedBadge
        {
            get { return ConfigurationHelper.ShareCopy_Facebook_EarnedBadge; }
        }

        // Completed challenge
        public static string ShareCopy_Facebook_CompletedChallenge
        {
            get { return "My furry friend and I just completed a Nature’s Recipe Pet Health Club challenge! Join us and your pet can live a healthier life while earning rewards!"; }
        }
        public static string ShareCopy_Twitter_CompletedChallenge
        {
            get { return "I just took a healthy challenge with my pet from @NaturesRecipe. Join #PetHealthClub and see if you can keep up!"; }
        }
        public static string ShareCopy_Pinterest_CompletedChallenge
        {
            get { return ConfigurationHelper.ShareCopy_Facebook_CompletedChallenge; }
        }

        // Unlocked Rewards
        public static string ShareCopy_Facebook_UnlockedRewards
        {
            get { return "My pet and I just won an amazing treat from Nature’s Recipe! Join Pet Health Club and you can start living healthier and earning rewards with your pet!"; }
        }
        public static string ShareCopy_Twitter_UnlockedRewards
        {
            get { return "I just earned a reward from @NaturesRecipe with my pet! Join #PetHealthClub and start winning while living a healthier life!"; }
        }
        public static string ShareCopy_Pinterest_UnlockedRewards
        {
            get { return ConfigurationHelper.ShareCopy_Facebook_UnlockedRewards; }
        }

        #endregion
    }
}