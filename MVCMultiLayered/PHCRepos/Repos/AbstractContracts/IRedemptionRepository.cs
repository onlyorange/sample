using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using PHCDto.DTO;
using PHCData.Data;

namespace PHCRepos.Repos.AbstractContracts
{
    public interface IRedemptionRepository
    {
        
        void AssignPrizeToUser(int userId, int prizeId);
        void MarkPrizeAsRedeemed(int userId, int prizeId);

        WallpaperDTO GetPrizeByPin(string ICGPin);
        WallpaperDTO GetPrize(int prizeId);

        RewardsDTO GetReward(int userId, int prizeId);
    }
}
