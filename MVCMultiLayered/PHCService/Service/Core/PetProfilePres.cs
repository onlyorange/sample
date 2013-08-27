using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCRepos.Repos.AbstractContracts;
using PHCRepos.Repos.Core;
using PHCDto.DTO;

using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;
using PHCService.Helpers;

namespace PHCService.Service.Core
{
   public class PetProfilePres:IPetProfilePres
    {
       private IPetProfileDbProc _ipdp;

       public PetProfilePres(IPetProfileDbProc ipc)
       {
           _ipdp = ipc;
       }

       public PetProfileDTO SavePetProf(PetProfileDTO dto)
       {
           return  _ipdp.SavePetProf(dto);
       }

       public PetProfileDTO GetPetDetails(int PID)
       {
           return _ipdp.GetPetDetails(PID);
       }


       public int Update_petProfile(ProfileDTO pdto)
       {
          return   _ipdp.Update_petProfile(pdto);
       }

       public ProfileDTO GetLastPetReg(int uid)
       {
           return _ipdp.GetLastPetReg(uid);
       }

       public int SaveFirstBadge(int uid,int pid)
       {
           return _ipdp.SaveFirstBadge(uid,pid);
       }

       public List<ChallengeDTO> ListChallenge()
       {
           return _ipdp.ListChallenge();
       }

       public ChallengeDTO GetChallenginfo(int id)
       {
           var model = _ipdp.GetChallenginfo(id);
           model.IsContestChallenge = IsContestChallenge(model.CHID);
           model.RewardId = GetRewardIdFromChallengeId(model.CHID);
           return model;
       }

       public List<ChallengeDTO> GetPetnames(int uid,int cid)
       {
           return _ipdp.GetPetnames(uid,cid);
       }

       public void SaveChallengeBadge(ChallengeDTO dt)
       {
            _ipdp.SaveChallengeBadge(dt);
       }

       public ChallengeDTO Chkpetbadge(int pid,int cid)
       {
           return _ipdp.Chkpetbadge(pid,cid);
       }

       public ChallengeDTO GetBadges(int uid)
       {
           return _ipdp.GetBadges(uid);
       }

       public ProfileDTO GetBadgeCount(int uid)
       {
           return _ipdp.GetBadgeCount(uid);
       }

       public ChallengeDTO CompleteCh(int ebid)
       {
           var model = _ipdp.CompleteCh(ebid);
           model.IsContestChallenge = IsContestChallenge(model.CHID);
           model.RewardId = GetRewardIdFromChallengeId(model.CHID);
           return model;
       }

       public int chkuser(int uid)
       {
           return _ipdp.chkuser(uid);
       }

       public PetProfileDTO SaveFirstPet(ProfileDTO dto)
       {
           return _ipdp.SaveFirstPet(dto);
       }

       public void UpdateFBStatus(int ebid)
       {
           _ipdp.UpdateFBStatus(ebid);
       }

       public ProfileDTO GetRewards(int uid)
       {
           return _ipdp.GetRewards(uid);
       }

       public ChallengeDTO chkchallinfo(int petid, int chid)
       {
           return _ipdp.chkchallinfo(petid, chid);
       }

       public ChallengeDTO PreviousChallenges()
       {
           return _ipdp.PreviousChallenges();
       }

       public int savepetprofile(ProfileDTO pdto)
       {
           return _ipdp.savepetprofile(pdto);
       }

       public string Delete_pet(int id)
       {
           return _ipdp.Delete_pet(id);
       }


       public ProfileDTO geteditpetprofile(int id)
       {
           return _ipdp.geteditpetprofile(id);
       }

       public ChallengeDTO petCompleteCh(int ebid)
       {
           return _ipdp.petCompleteCh(ebid);
       }

       public ChallengeDTO InCompleteCh(int bid)
       {
           return _ipdp.InCompleteCh(bid);
       }

       public ChallengeDTO CHNotify(int uid)
       {
           var model = _ipdp.CHNotify(uid);
           model.IsContestChallenge = IsContestChallenge(model.CHID);
           model.RewardId = GetRewardIdFromChallengeId(model.CHID);
           return model;
       }

       public int ChUserCnt(int cid, int uid)
       {
           return _ipdp.ChUserCnt(cid, uid);
       }

       public ChallengeDTO CHDExists(int chid)
       {
           return _ipdp.CHDExists(chid);
       }

       public void PinUpdate(int ebid)
       {
           _ipdp.PinUpdate(ebid);
       }

       public void TweetUpdate(int eid)
       {
           _ipdp.TweetUpdate(eid);
       }

       public RewardsDTO GetWallPaperList()
       {
           return _ipdp.GetWallPaperList();
       }

       public string UpdateRewards(int uid,int wid)
       {
          return  _ipdp.UpdateRewards(uid,wid);
       }

       public bool UserHasBadge(int uid, int bid)
       {
           return _ipdp.UserHasBadge(uid, bid);
       }


       public ChallengeDTO GetEarnedBadgeByChallenge(int uid, int cid)
       {
           var model = _ipdp.GetEarnedBadgeByChallenge(uid, cid);
           
           model.IsContestChallenge = IsContestChallenge(model.CHID);
           model.RewardId = GetRewardIdFromChallengeId(model.CHID);
           return model;
       }

       public bool AssignBadge(int userId, int badgeId, int? petId = null)
       {
           return _ipdp.AssignBadge(userId, badgeId, petId);
       }

        #region Contest Helpers

       /// <summary>
       /// Quick helper method for determing if a contest is a contest challenge or not
       /// </summary>
       /// <param name="challengeId"></param>
       /// <returns></returns>
       public bool IsContestChallenge(int challengeId)
       {
           int[] contest = new int[] { 13, 10, 15, 12, 20, ContestHelper.NailedItId };
           return contest.Contains(challengeId);
       }

       /// <summary>
       /// Returns the associated reward/wallpaper id that goes along with this challenge
       /// </summary>
       /// <param name="challengeId"></param>
       /// <returns></returns>
       public int GetRewardIdFromChallengeId(int challengeId)
       {
           switch (challengeId)
           {
               case ContestHelper.FitAndFancyFreeId:
                   return RewardHelper.DogTagId;
               case ContestHelper.QualityTimeId:
                   return RewardHelper.DogTagId;
               case ContestHelper.HealthySnacksId:
                   return RewardHelper.DogTagId;

               case ContestHelper.StayHydratedId:
                   return RewardHelper.DogBowlId;
               case ContestHelper.PearlyWhitesId:
                   return RewardHelper.DogBowlId;

               case ContestHelper.NailedItId:
                   return RewardHelper.GiftCardId;
           }

           return 0;
       }

       /// <summary>
       /// Returns a human readable name for the prize associated with a given challenge
       /// </summary>
       /// <param name="challengeId"></param>
       /// <returns></returns>
       public string GetRewardNameFromChallengeId(int challengeId)
       {
           switch (challengeId)
           {
                //Dog Tags
               case ContestHelper.FitAndFancyFreeId:
                   return RewardHelper.DogTagName;
               case ContestHelper.QualityTimeId:
                   return RewardHelper.DogTagName;
               case ContestHelper.HealthySnacksId:
                   return RewardHelper.DogTagName;
                // DogBowls
               case ContestHelper.StayHydratedId:
                   return RewardHelper.DogBowlName;
               case ContestHelper.PearlyWhitesId:
                   return RewardHelper.DogBowlName;
                //GiftCard
               case ContestHelper.NailedItId:
                   return RewardHelper.GiftCardName;
           }
           return "";
       }



        #endregion
    }
}
