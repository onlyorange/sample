using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using PHCRepos.Repos.AbstractContracts;
using PHCData.Data;
using PHCDto.DTO;

namespace PHCRepos.Repos.Core
{
    public class RedemptionRepository : IRedemptionRepository
    {
        #region Mapping functions
        /// <summary>
        /// Simple map function
        /// </summary>
        /// <param name="source"></param>
        /// <returns></returns>
        private WallpaperDTO MapWallpapperToWallpaperDTO(Wallpaper prize)
        {
           return new WallpaperDTO()
            {
                BadgesNeeded = prize.BadgesNeeded.HasValue ? prize.BadgesNeeded.Value : 0,
                DateUnlockes = prize.DateUnlocks.HasValue ? prize.DateUnlocks.Value : DateTime.Now,
                ICGPin = prize.ICGPin,
                Title = prize.Title,
                Wallpaper = prize.WallImage,
                WID = prize.WID
            };
        }

        private RewardsDTO MapRewardToRewardDTO(Reward reward)
        {
            return new RewardsDTO()
            {
                 DateClaimed = reward.DateClaimed,
                 DateUnlocked = reward.DateUnlocked,
                 RID = reward.RID,
                 UID = reward.UID.Value,
                 WID = reward.WID.Value

            };
        }

        #endregion

        #region Prizing
        /// <summary>
        /// Gets a prize by Id
        /// </summary>
        /// <param name="prizeId"></param>
        /// <returns></returns>
        public WallpaperDTO GetPrize(int prizeId)
        {
            using (var context = new PHCEntities())
            {
                var prize = context.Wallpapers.Where(x => x.WID == prizeId).FirstOrDefault();
                if (prize == null)
                    throw new Exception("Prize not found");

                return MapWallpapperToWallpaperDTO(prize);
            }
        }

        /// <summary>
        /// Get prize by a pin
        /// </summary>
        /// <param name="ICGPin"></param>
        /// <returns></returns>
        public WallpaperDTO GetPrizeByPin(string ICGPin)
        {
            using (var context = new PHCEntities())
            {
                var prize = context.Wallpapers.Where(x => x.ICGPin == ICGPin).FirstOrDefault();
                if (prize == null)
                    throw new Exception("Prize not found");

                return MapWallpapperToWallpaperDTO(prize);
            }
        }

        /// <summary>
        /// Indicates that a user is now eligable for a particular reward/prize
        /// </summary>
        /// <param name="userId"></param>
        /// <param name="prizeId"></param>
        public void AssignPrizeToUser(int userId, int prizeId)
        {
            using (var context = new PHCEntities())
            {
                var reward = context.Rewards.Where(x => x.WID == prizeId && x.UID == userId).FirstOrDefault();
                if (reward == null) // if there's not already an entry, then let's assign this prize
                {
                    reward = new Reward()
                    {
                        DateUnlocked = DateTime.UtcNow,
                        UID = userId,
                        WID = prizeId
                    };
                    context.Rewards.AddObject(reward);
                    context.SaveChanges();
                }
            }
        }
        
        /// <summary>
        /// Indicates this prize has been received
        /// </summary>
        /// <param name="userId"></param>
        /// <param name="prizeId"></param>
        public void MarkPrizeAsRedeemed(int userId, int prizeId)
        {
            using (var context = new PHCEntities())
            {
                var reward = context.Rewards.Where(x => x.UID == userId && x.WID == prizeId).FirstOrDefault();
                if (reward == null)
                    return;

                reward.DateClaimed = DateTime.UtcNow;
                context.SaveChanges();
            }
        }

        #endregion

        /// <summary>
        /// Returns the user specfic reward of this prize
        /// </summary>
        /// <param name="userId"></param>
        /// <param name="prizeId"></param>
        /// <returns></returns>
        public RewardsDTO GetReward(int userId, int prizeId)
        {
            using (var context = new PHCEntities())
            {
                var prize = context.Rewards.Where(x => x.WID == prizeId && x.UID == userId).FirstOrDefault();
                if (prize == null)
                    return new RewardsDTO();

                return MapRewardToRewardDTO(prize);
            }
        }
    }
}
