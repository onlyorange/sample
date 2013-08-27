using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using PHCDto.DTO;
using PHCService.Service.InterfaceContracts;
using System.Security.Cryptography;
using PHCRepos.Repos.AbstractContracts;
using PHCRepos.Repos.Core;

namespace PHCService.Service.Core
{
    public class RedemptionService : IRedemptionService
    {
        public IRedemptionRepository _repo { get; set; }

        public RedemptionService(IRedemptionRepository repo)
        {
            _repo = repo;
        }

        public RedemptionService()
        {
            _repo = new RedemptionRepository();
        }


        #region ICG Integration

        /// <summary>
        /// Computes the HMAC-SHA256 signature of the provided queryString pased on the pre-defined private key
        /// </summary>
        /// <param name="queryString"></param>
        /// <returns></returns>
        public string ComputeSignature(string queryString, string key)
        {
            ASCIIEncoding encoding = new ASCIIEncoding();
            
            byte[] keyByte = encoding.GetBytes(key);
            var hmac = new HMACSHA256(keyByte);

            var resultBytes = hmac.ComputeHash(encoding.GetBytes(queryString));
            
            string signature = ""; // convert back to normal encoding
            for (int i = 0; i < resultBytes.Length; i++)
                signature += resultBytes[i].ToString("X2"); // hex format
            
            return signature;
        }

        /// <summary>
        /// Creates the properly formated query string that we'll use to make the signature
        /// </summary>
        /// <param name="facebookId"></param>
        /// <param name="pin"></param>
        /// <param name="callback"></param>
        /// <returns></returns>
        public string CreateQueryString(string facebookId, string pin, string callback)
        {
            return string.Format("f={0}&p={1}&cb={2}", facebookId, pin, callback);
        }
        
        #endregion

        #region Assign and Confirm Prizes

        public WallpaperDTO GetPrize(int prizeId)
        {
            return _repo.GetPrize( prizeId );
        }
        
        /// <summary>
        /// Gets a prize (wallpaper -.-) by the ICG Pin
        /// </summary>
        /// <param name="pin"></param>
        /// <returns></returns>
        public WallpaperDTO GetPrizeByPin(string pin)
        {
            return _repo.GetPrizeByPin(pin);
        }

        public void AssignPrizeToUser(int userId, int prizeId)
        {
            _repo.AssignPrizeToUser(userId, prizeId);
        }
       
        /// <summary>
        /// Given a token, this will confirm and invalidate a token
        /// </summary>
        /// <param name="token"></param>
        /// <returns></returns>
        public bool ConfirmPrize(
            int userId
            , int prizeId
            )
        {
            // update this user's status as receiving this prize
            _repo.MarkPrizeAsRedeemed(userId, prizeId);

            return true;
        }

        #endregion

        #region Reddemed Rewards

        public bool IsEligableForReward(int userId, int prizeId)
        {
            // get the reward by user & prize
            var reward = _repo.GetReward( userId, prizeId );
            if (reward == null || reward.WID == null) // if there's no reward, then we're good
                return true;

            // see if this user has already gotten this reward
            if (reward.DateClaimed.HasValue && reward.DateClaimed.Value > DateTime.MinValue)
                return false;

            // Otherwise it looks like they're good to go
            return true;
        }

        public RewardsDTO GetReward(int p, int id)
        {
            return new RewardsDTO();
        }

        #endregion

    }
}
